<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Model\Svg\SvgFile;
use App\Model\Title;
use App\Service\FileCache;
use App\Service\Renderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * Serve a PNG rendering of the given SVG in the given language (without any user-provided
     * translation strings).
     *
     * @Route("/api/file/{filename}/{lang}.png", name="api_file", methods="GET")
     *
     * @param string $filename
     * @param string $lang
     * @return Response
     */
    public function getFile(string $filename, string $lang): Response
    {
        $filename = Title::normalize($filename);
        $content = $this->svgRenderer->render($this->cache->getPath($filename), $lang);
        return new Response($content, 200, [
            'Content-Type' => 'image/png',
            'X-File-Hash' => sha1($content),
        ]);
    }

    /**
     * Get a full filesystem path to a temporary file.
     * @param string $filename The base SVG filename.
     * @param string $key The unique key to append to the filename.
     * @param string $ext The file extension to use for the returned filename.
     * @return string
     */
    protected function getTempFilename(string $filename, string $key, string $ext): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        return $this->cache->fullPath($name.'_'.$key.'.'.$ext);
    }

    /**
     * Take POST data, write new translations into the SVG file, render a resultant PNG out to the
     * filesystem, and return an identifier for that PNG file.
     *
     * @Route("/api/translate/{filename}/{lang}", name="api_file_request", methods="POST")
     */
    public function requestFileWithTranslations(string $filename, string $lang, Request $request): Response
    {
        // Get the SVG file, and add in the new translations. The traslations come from the form and
        // are already in the form of a mapping of tspan-IDs to language strings. Only a single
        // language's translations can be set at a time (which is fine here, because we're only ever
        // receiving one language at a time).
        $filename = Title::normalize($filename);
        $path = $this->cache->getPath($filename);
        $file = new SvgFile($path);
        $translations = $request->request->all();
        $file->setTranslations($lang, $translations);

        // Write the modified SVG out to the filesystem, named with a unique key. This is necessary
        // both because multiple people could be translating the same file at the same time, and
        // also means we can use the same key for the rendered PNG file. The key is generated from
        // the translation set in order to not generate redundant cache files.
        $fileKey = md5(serialize($translations));
        $tempPngFilename = $this->getTempFilename($filename, $fileKey, 'png');
        $tempSvgFilename = $this->getTempFilename($filename, $fileKey, 'svg');
        $file->saveToPath($tempSvgFilename);

        // Render the modified SVG to PNG, and return it's URL.
        $this->svgRenderer->render($tempSvgFilename, $lang, $tempPngFilename);
        $relativeUrl = $this->generateUrl('api_file_translated', [
            'filename' => $filename,
            'key' => $fileKey,
            'lang' => $lang,
        ]);
        return new JsonResponse(['imageSrc' => $relativeUrl]);
    }

    /**
     * Get a request for a already-rendered, custom-translated PNG (identified by a key), and
     * return that file.
     *
     * @Route("/api/file/{filename}/{lang}/{key}.png", name="api_file_translated", methods="GET")
     *
     * @param string $filename
     * @param Request $request
     * @return Response
     */
    public function getFileWithTranslations(string $filename, string $lang, string $key, Request $request): Response
    {
        $tempPngFilename = $this->getTempFilename($filename, $key, 'png');
        if (file_exists($tempPngFilename)) {
            return new BinaryFileResponse($tempPngFilename, 200, ['X-Accel-Buffering' => 'no']);
        }
        throw new NotFoundHttpException();
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
}
