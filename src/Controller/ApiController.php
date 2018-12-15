<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Model\Svg\SvgFile;
use App\Model\Title;
use App\Service\FileCache;
use App\Service\Renderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * API endpoint
 */
class ApiController extends AbstractController
{

    /** @var FileCache */
    private $cache;

    /** @var Renderer */
    protected $svgRenderer;

    public function __construct(FileCache $cache, Renderer $svgRenderer)
    {
        $this->cache = $cache;
        $this->svgRenderer = $svgRenderer;
    }

    /**
     * Serve a PNG rendering of the given SVG in the given language.
     * @Route("/api/file/{filename}/{lang}.png", name="api_file", methods="GET")
     *
     * @param string $filename
     * @return Response
     */
    public function getFile(string $filename, string $lang): Response
    {
        $filename = Title::normalize($filename);
        return $this->serveContent($this->cache->getPath($filename), $lang);
    }

    /**
     * Serve a PNG rendering of the given SVG in the given language, based on the POSTed translations.
     * @Route("/api/file/{filename}/{lang}.png", name="api_file_translated", methods="POST")
     * @param string $filename
     * @param Request $request
     * @return Response
     */
    public function getFileWithTranslations(string $filename, string $lang, Request $request): Response
    {
        $filename = Title::normalize($filename);
        $json = $request->getContent();
        if ('' === $json) {
            return $this->getFile($filename, $lang);
        }

        $translations = \GuzzleHttp\json_decode($json, true);
        $path = $this->cache->getPath($filename);
        $file = new SvgFile($path);
        $file->switchToTranslationSet($translations);

        // Write SVG file to filesystem, so it can be converted by rsvg. Use a unique filename
        // because multiple users can be translating the same file at the same time (whereas
        // getFile() above only ever serves the one version of a file).
        $tmpSvgFilename = $this->cache->getTempSvgFile();
        $file->saveToPath($tmpSvgFilename);

        return $this->serveContent($tmpSvgFilename, $lang);
    }

    /**
     * @Route("/api/translations/{fileName}", name="api_translations", methods="GET")
     *
     * @param string $fileName
     * @return Response
     */
    public function getTranslations(string $fileName): Response
    {
        $fileName = Title::normalize($fileName);
        $path = $this->cache->getPath($fileName);
        $file = new SvgFile($path);

        return $this->json($file->getInFileTranslations());
    }

    /**
     * @Route("/api/languages/{fileName}", name="api_languages", methods="GET")
     *
     * @param string $fileName
     * @return Response
     */
    public function getLanguages(string $fileName): Response
    {
        $fileName = Title::normalize($fileName);
        $path = $this->cache->getPath($fileName);
        $file = new SvgFile($path);
        $langs = $file->getSavedLanguages();
        sort($langs);

        return $this->json($langs);
    }

    /**
     * Serves an SVG as a PNG.
     *
     * @param string $svgFilename Full path to the SVG file to convert and serve as a PNG.
     * @param string $lang The language to use for the SVG's text.
     * @return Response
     */
    private function serveContent(string $svgFilename, string $lang): Response
    {
        $content = $this->svgRenderer->render($svgFilename, $lang);
        return new Response($content, 200, [
            'Content-Type' => 'image/png',
            'X-File-Hash' => sha1($content),
        ]);
    }
}
