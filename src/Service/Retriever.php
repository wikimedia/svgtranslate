<?php

declare(strict_types = 1);

namespace App\Service;

use App\Exception\InvalidFormatException;
use GuzzleHttp\Client;

/**
 * Service that retrieves files from an external wiki
 */
class Retriever
{
    /** @var MediaWikiApi */
    private $api;

    public function __construct(MediaWikiApi $api)
    {
        $this->api = $api;
    }

    /**
     * Retrieve a file
     *
     * @param string $fileName
     * @return string File contents
     */
    public function retrieve(string $fileName): string
    {
        $info = $this->api->imageInfo($fileName);
        $url = reset($info['imageinfo'])['url'];
        return $this->httpGet($url);
    }

    /**
     * Download content from the given URL
     *
     * @param string $url
     * @return string
     */
    protected function httpGet(string $url): string
    {
        $client = new Client();
        $response = $client->get($url);
        $contentType = $response->getHeader('Content-Type');
        $contentType = reset($contentType);
        if ('image/svg+xml' !== $contentType) {
            throw new InvalidFormatException($contentType);
        }
        return $response->getBody()->getContents();
    }
}
