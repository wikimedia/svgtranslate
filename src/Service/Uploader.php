<?php
declare(strict_types = 1);

namespace App\Service;

/**
 * Service that retrieves files from an external wiki
 */
class Uploader
{
    /** @var MediaWikiApi */
    protected $api;

    /** @var FileCache */
    protected $fileCache;

    public function __construct(MediaWikiApi $api, FileCache $fileCache)
    {
        $this->api = $api;
        $this->fileCache = $fileCache;
    }

    /**
     * Upload a file to the remote wiki, and delete it from the local cache.
     *
     * @param string $file The full filesystem path of the file to upload.
     * @param string $destinationFilename The title to give the file on the wiki.
     * @param string $comment The upload revision comment.
     * @return string The full URL to the file's page on Commons.
     */
    public function upload(string $file, string $destinationFilename, string $comment): string
    {
        $uploadDetails = $this->api->upload($file, $destinationFilename, $comment);
        $this->fileCache->delete($destinationFilename);
        return $uploadDetails->imageinfo->descriptionurl;
    }
}
