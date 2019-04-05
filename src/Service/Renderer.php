<?php
declare(strict_types = 1);

namespace App\Service;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * SVG to PNG rendering service.
 */
class Renderer
{

    /** @var string */
    protected $rsvgCommand;

    /**
     * @param string $rsvgCommand The command to execute to do the conversion.
     */
    public function __construct(string $rsvgCommand)
    {
        $this->rsvgCommand = $rsvgCommand;
    }

    /**
     * @param string $file Full filesystem path to the SVG file to render.
     * @param string $lang Code of the language in which to render the image.
     * @throws ProcessFailedException If the PNG conversion failed.
     * @return string The PNG image contents.
     */
    public function render(string $file, string $lang) : string
    {
        $process = new Process([$this->rsvgCommand, $file]);
        if ('fallback' !== $lang) {
            // Set the LANG environment variable, which will be interpreted as the SVG
            // systemLanguage. If the fallback language is being requested, the OS's default will be
            // used instead (as is done in MediaWiki).
            $process->setEnv(['LANG' => $lang]);
        }
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        return $process->getOutput();
    }
}
