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
     * Render a SVG file to PNG and either save a file or return the image.
     * @param string $file Full filesystem path to the SVG file to render.
     * @param string $lang Code of the language in which to render the image.
     * @param string $outFile Full filesystem path to the file to write the PNG to.
     * @throws ProcessFailedException If the PNG conversion failed.
     * @return string The PNG image contents, or nothing if an $outFile was provided.
     */
    public function render(string $file, string $lang, ?string $outFile = null) : string
    {
        // Construct the command, using variables that will be escaped when it's run.
        $command = $this->rsvgCommand.' "$SVG"';
        if ('fallback' !== $lang) {
            // Set the language to use from the SVG systemLanguage.
            // If the fallback language is being requested, the OS's default will be
            // used instead (as is done in MediaWiki).
            $command .= " --accept-language=$lang";
        }
        if ($outFile) {
            // Redirect to output file if required.
            $command .= ' > "$PNG"';
        }
        $process = Process::fromShellCommandline($command);
        $process->mustRun(null, ['SVG' => $file, 'PNG' => $outFile]);
        return $process->getOutput();
    }
}
