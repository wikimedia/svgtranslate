<?php

declare(strict_types = 1);

namespace App\Service;

use App\Model\Svg\SvgFile;
use Psr\Log\LoggerInterface;

/**
 * Instantiates SvgFile objects
 */
class SvgFileFactory
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Creates an instance of SvgFile for the given path
     *
     * @param string $path
     * @return SvgFile
     * @throws \App\Exception\SvgLoadException
     */
    public function create(string $path): SvgFile
    {
        return new SvgFile($path, $this->logger);
    }
}
