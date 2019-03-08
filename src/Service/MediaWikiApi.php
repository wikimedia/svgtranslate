<?php
declare(strict_types = 1);

namespace App\Service;

use App\Exception\ImageNotFoundException;
use CURLFile;
use Exception;
use GuzzleHttp\Client;
use MediaWiki\OAuthClient\Client as OauthClient;
use MediaWiki\OAuthClient\Token;
use stdClass;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * A service for interacting with MediaWiki API
 */
class MediaWikiApi
{
    /**
     * @var string Fully qualified URL of the site's api.php
     */
    private $entryPoint;

    /** @var OauthClient */
    protected $oauthClient;

    /** @var Token */
    protected $oauthAccessToken;

    /**
     * @param string $entryPoint Fully-qualified URL of the wiki's api.php
     */
    public function __construct(string $entryPoint, OauthClient $client, Session $session)
    {
        $this->entryPoint = $entryPoint;
        $this->oauthClient = $client;
        $this->oauthAccessToken = $session->get('oauth.access_token');
    }

    /**
     * API action=query&prop=imageinfo wrapper
     *
     * @param string $fileName Already normalized filename without a namespace
     * @param string[] $props File properties to retrieve, corrsponds to imageinfo's iiprop parameter
     * @return mixed[] Associative array of file properties, as returned by imageinfo
     * @throws ImageNotFoundException
     */
    public function imageInfo(string $fileName, array $props = ['url']): array
    {
        $response = $this->request([
            'action' => 'query',
            'prop' => 'imageinfo',
            'iiprop' => implode('|', $props),
            'titles' => "File:$fileName",
        ]);

        if (!isset($response['query']['pages'])
            || 1 !== count($response['query']['pages'])
            || isset(reset($response['query']['pages'])['missing'])
            || isset(reset($response['query']['pages'])['invalid'])
        ) {
            throw new ImageNotFoundException($fileName);
        }

        return reset($response['query']['pages']);
    }

    /**
     * Performs an API request
     *
     * @param string[] $params API parameters
     * @param string $method HTTP method
     * @return mixed[] API response deserialized into an associative array
     */
    protected function request(array $params, string $method = 'GET'): array
    {
        $params['format'] = 'json';
        $params['formatversion'] = 2;
        $client = new Client(['base_uri' => $this->entryPoint]);
        $requestOptions = [
            'query' => $params,
            'headers' => [
                'User-Agent' => 'SvgTranslate - https://github.com/wikimedia/svgtranslate',
            ],
        ];
        $response = $client->request($method, '', $requestOptions);
        return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Upload a file to the OAuth wiki.
     * @param string $file The filesystem path to the file to upload.
     * @param string $destinationFilename The filename to give the file on the wiki.
     * @param string $comment Upload revision comment.
     * @return stdClass Information about the upload.
     * @throws Exception If the CSRF token can not be retrieved or the upload was not successful.
     */
    public function upload(string $file, string $destinationFilename, string $comment): stdClass
    {
        // 1. Get the CSRF token.
        $csrfTokenParams = [
            'format' => 'json',
            'action' => 'query',
            'meta' => 'tokens',
            'type' => 'csrf',
        ];
        $csrfTokenResponse = $this->oauthClient->makeOAuthCall(
            $this->oauthAccessToken,
            $this->entryPoint,
            true,
            $csrfTokenParams
        );
        $csrfTokenData = \GuzzleHttp\json_decode($csrfTokenResponse);
        if (!isset($csrfTokenData->query->tokens->csrftoken)) {
            throw new Exception("Unable to get CSRF token from: $csrfTokenResponse");
        }

        // 2. Upload the file.
        $uploadParams = [
            'format' => 'json',
            'action' => 'upload',
            'filename' => $destinationFilename,
            'token' => $csrfTokenData->query->tokens->csrftoken,
            'comment' => $comment,
            'filesize' => filesize($file),
            'file' => new CURLFile($file),
            // We have to ignore warnings so that we can overwrite the existing image.
            'ignorewarnings' => true,
        ];
        $uploadResponse = $this->oauthClient->makeOAuthCall(
            $this->oauthAccessToken,
            $this->entryPoint,
            true,
            $uploadParams
        );
        $uploadResponseData = \GuzzleHttp\json_decode($uploadResponse);
        if (isset($uploadResponseData->error->info)) {
            // Throw any returned error.
            throw new HttpException(500, $uploadResponseData->error->info);
        }
        if (!isset($uploadResponseData->upload->result) || 'Success' !== $uploadResponseData->upload->result) {
            // Just in case something else went wrong and there's no actual error response.
            throw new Exception('Upload failed. Response was: '.$uploadResponse);
        }

        return $uploadResponseData->upload;
    }
}
