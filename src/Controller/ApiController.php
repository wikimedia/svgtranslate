<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Model\Svg\SvgFile;
use App\Model\Title;
use App\Service\FileCache;
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

    public function __construct(FileCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @Route("/api/file/{fileName}", name="api_file", methods="GET")
     *
     * @param string $fileName
     * @return Response
     */
    public function getFile(string $fileName): Response
    {
        $fileName = Title::normalize($fileName);
        $content = $this->cache->getContent($fileName);
        return $this->serveContent($content);
    }

    /**
     * @Route("/api/file/{fileName}", name="api_file_translated", methods="POST")
     * @param string $fileName
     * @param Request $request
     * @return Response
     */
    public function getFileWithTranslations(string $fileName, Request $request): Response
    {
        $fileName = Title::normalize($fileName);
        $json = $request->getContent();
        if ('' === $json) {
            return $this->getFile($fileName);
        }

        $translations = \GuzzleHttp\json_decode($json, true);
        $path = $this->cache->getPath($fileName);
        $file = new SvgFile($path, 'en');
        $file->switchToTranslationSet($translations);

        return $this->serveContent($file->saveToString());
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
        $file = new SvgFile($path, 'en');

        return $this->json($file->getInFileTranslations());
    }

    /**
     * Serves an SVG
     *
     * @param string $content
     * @return Response
     */
    private function serveContent(string $content): Response
    {
        return new Response($content, 200, [
            'Content-Type' => 'image/svg+xml',
            'X-File-Hash' => sha1($content),
        ]);
    }
}
