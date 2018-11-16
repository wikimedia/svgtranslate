<?php
declare(strict_types = 1);

namespace App\Service;

/**
 * This class manages cache for files being translated
 */
class FileCache
{
    /**
     * 5 hours
     */
    private const CACHE_DURATION = 5 * 60 * 60;

    /** @var Retriever */
    private $retriever;

    /** @var string */
    private $directory;

    /**
     * @param Retriever $retriever
     * @param string $directory Cache directory
     */
    public function __construct(Retriever $retriever, string $directory)
    {
        $this->retriever = $retriever;
        $this->directory = $directory;
    }

    /**
     * Returns a path to the cached file, downloading it if needed.
     * If the file is not found on Commons, an exception will be thrown.
     *
     * @param string $fileName
     * @return string
     */
    public function getPath(string $fileName): string
    {
        $this->tick();

        $path = $this->fullPath($fileName);

        if (!$this->statFile($path)) {
            $content = $this->retriever->retrieve($fileName);
            file_put_contents($path, $content);
        }

        return $path;
    }

    /**
     * Returns content of the given file
     *
     * @param string $fileName
     * @return string
     */
    public function getContent(string $fileName): string
    {
        return file_get_contents($this->getPath($fileName));
    }

    /**
     * Purges file cache of stale files with 1/100 probability, to prevent
     * too many slowdowns
     */
    protected function tick(): void
    {
        if (42 !== mt_rand(1, 100)) {
            return;
        }

        foreach (glob($this->fullPath('*.svg')) as $fileName) {
            $this->statFile($fileName);
        }
    }

    /**
     * Checks whether the given file is present locally and not stale.
     * Deletes the file if it's stale.
     *
     * @param string $fileName
     * @return bool Whether the file is good to go
     */
    protected function statFile(string $fileName): bool
    {
        $stat = @stat($fileName);
        if (!$stat) {
            return false;
        }
        if ($stat['mtime'] + self::CACHE_DURATION < time()) {
            unlink($fileName);
            return false;
        }

        return true;
    }

    /**
     * Returns a full path for the given file
     *
     * @param string $fileName
     * @return string
     */
    protected function fullPath(string $fileName): string
    {
        return $this->directory.DIRECTORY_SEPARATOR.$fileName;
    }
}
