<?php
declare(strict_types = 1);

namespace App\Service;

use App\Exception\ImageNotFoundException;
use GuzzleHttp\Client;

/**
 * A service for interacting with MediaWiki API
 */
class MediaWikiApi
{
    /**
     * @var string Fully qualified URL of the site's api.php
     */
    private $entryPoint;

    /**
     * @param string $entryPoint Fully-qualified URL of the wiki's api.php
     */
    public function __construct(string $entryPoint)
    {
        $this->entryPoint = $entryPoint;
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
}
