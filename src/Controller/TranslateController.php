<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Exception\ImageNotFoundException;
use App\Exception\InvalidFormatException;
use App\Exception\SvgLoadException;
use App\Exception\SvgStructureException;
use App\Model\Svg\SvgFile;
use App\Model\Title;
use App\OOUI\TranslationsFieldset;
use App\Service\FileCache;
use App\Service\SvgFileFactory;
use App\Service\Uploader;
use GuzzleHttp\Exception\RequestException;
use Krinkle\Intuition\Intuition;
use OOUI\ButtonInputWidget;
use OOUI\DropdownInputWidget;
use OOUI\HorizontalLayout;
use OOUI\LabelWidget;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TranslateController extends AbstractController
{
    protected $svgFactory;

    public function __construct(SvgFileFactory $svgFactory)
    {
        $this->svgFactory = $svgFactory;
    }

    /**
     * The translate page.
     *
     * The 'prefix' part of this route is actually a fixed string 'File', but in order to make it
     * case-insensitive we have to make it a route variable with a regex requirement. Then we can
     * also check it and redirect to the canonical form when required.
     * @Route( "/{prefix}:{filename}",
     *     name="translate",
     *     methods={"GET"},
     *     requirements={"prefix"="(?i:File)", "filename"="(.+)"}
     *     )
     */
    public function translate(
        Request $request,
        Intuition $intuition,
        Session $session,
        FileCache $cache,
        string $filename,
        string $prefix = 'File'
    ): Response {
        $normalizedFilename = Title::normalize($filename);

        // Redirect to normalized URL if required.
        if ('File' !== $prefix || $filename !== $normalizedFilename) {
            return $this->redirectToRoute('translate', ['filename' => $normalizedFilename]);
        }

        // Fetch the SVG from Commons.
        try {
            $this->assertFileType($normalizedFilename);
            $path = $cache->getPath($filename);
            $svgFile = $this->svgFactory->create($path);
        } catch (ImageNotFoundException $exception) {
            return $this->showError('not-found', $normalizedFilename);
        } catch (InvalidFormatException $exception) {
            return $this->showError('invalid-format', $normalizedFilename);
        } catch (SvgLoadException $exception) {
            return $this->showError('invalid-svg', $normalizedFilename);
        } catch (RequestException $exception) {
            return $this->showError('network-error', $normalizedFilename);
        } catch (SvgStructureException $exception) {
            $msgParams = $exception->getMessageParams();
            // If there's no element ID fall back to the no-ID placeholder message.
            if (null === $msgParams[0]) {
                $msgParams[0] = $intuition->msg('structure-error-no-id');
            }
            return $this->showError(
                $exception->getMessage(),
                $normalizedFilename,
                ['msg_params' => $msgParams]
            );
        }

        // If there are no strings to translate, tell the user.
        //   - If they've come from the search form redirect then back there with an error.
        //   - If they've come directly to this page, just show the message here.
        // The flash message is checked first so that it is always cleared.
        $translations = $svgFile->getInFileTranslations();
        $isSearchRedirect = $session->getFlashBag()->get('search-redirect');
        $noTranslationsMessage = null;
        if (0 === count($translations)) {
            $noTranslationsMessage = $this->renderView(
                'error_message.html.twig',
                ['msg_name' => 'no-translations', 'msg_params' => [] ]
            );
            if ($isSearchRedirect) {
                $this->addFlash('search-errors', (string)$noTranslationsMessage);
                // Also flash the failed filename so we can populate the search form.
                $this->addFlash('search-redirect', $normalizedFilename);
                return $this->redirectToRoute('home');
            }
        }

        // Upload and download buttons.
        $downloadButton = new ButtonInputWidget([
            'label' => $intuition->msg('download-button-label'),
            'flags' => [ 'progressive' ],
            'type' => 'submit',
            'framed' => false,
            'id' => 'download-button-widget',
            'infusable' => true,
        ]);
        $uploadButton = new ButtonInputWidget([
            'label' => $intuition->msg('upload-button-label'),
            'flags' => [ 'progressive' ],
            'type' => 'submit',
            'icon' => 'logoWikimediaCommons',
            'name' => 'upload',
            'id' => 'upload-button-widget',
            'infusable' => true,
        ]);
        $user = $session->get('logged_in_user');
        if (!$user) {
            // Only logged in users can upload.
            $uploadButton->setDisabled(true);
        }

        // Source and target language selectors.
        $availableLangs = [
            ['data' => 'fallback', 'label' => $intuition->msg('default-language')],
        ];
        foreach ($svgFile->getSavedLanguages() as $lang) {
            $langName = $intuition->getLangName($lang);
            if ($langName) {
                $availableLangs[] = [
                    'data' => $lang,
                    'label' => $langName,
                ];
            }
        }
        $sourceLang = new DropdownInputWidget([
            'label' => $intuition->msg('source-lang-label'),
            'options' => $availableLangs,
            // @TODO Get this value from the session.
            'value' => 'fallback',
            'classes' => ['source-lang-widget'],
            'infusable' => true,
        ]);
        $targetLangDefault = 'fallback';
        $targetLangLabel = $intuition->msg('select-language');
        $targetLang = new ButtonInputWidget([
            'label' => $targetLangLabel,
            'value' => $targetLangDefault,
            'classes' => ['target-lang-widget'],
            'indicator' => 'down',
            'infusable' => true,
            'name' => 'target-lang',
        ]);
        $languageSelectorsLayout = new HorizontalLayout([
            'items' => [
                $sourceLang,
                new LabelWidget([
                    'label' => $intuition->msg('source-to-target'),
                    'classes' => ['source-to-target-label'],
                ]),
                $targetLang,
            ],
            'classes' => ['language-selectors'],
        ]);

        // Form fields for translation messages are in a fieldset which contains fieldsets for each group of messages.
        $translationsFieldset = new TranslationsFieldset([
            'translations' => $translations,
            'source_lang_code' => $sourceLang->getValue(),
            'target_lang_code' => $targetLang->getValue(),
            'disabled' => 'fallback' === $targetLangDefault,
        ]);
        $wiki = parse_url($this->getParameter('wiki_url'))['host'];

        return $this->render('translate.html.twig', [
            'page_class' => 'translate',
            'title' => Title::text($filename),
            'filename' => $normalizedFilename,
            'translation_fieldset' => $translationsFieldset,
            'download_button' => $downloadButton,
            'upload_button' => $uploadButton,
            'language_selectors' => $languageSelectorsLayout,
            'translations' => $translations,
            'target_lang' => $targetLangDefault,
            'wiki' => $wiki,
            'no_translations_message' => $noTranslationsMessage,
        ]);
    }

    /**
     * @Route( "/File:{filename}", name="updownload", methods={"POST"})
     */
    public function updownload(
        string $filename,
        Request $request,
        FileCache $cache,
        Uploader $uploader
    ): Response {
        $requestParams = $request->request->all();

        // Are we uploading or downloading?
        $isUpload = false;
        if (isset($requestParams['upload'])) {
            $isUpload = true;
            unset($requestParams['upload']);
        }

        // Target language.
        $targetLang = $requestParams['target-lang'];
        unset($requestParams['target-lang']);

        // Add the translations to the file and save it to the filesystem.
        $file = new SvgFile($cache->getPath(Title::normalize($filename)));
        $file->setTranslations($targetLang, $requestParams);
        $tmpFilename = $cache->fullPath($filename.uniqid().'.svg');
        file_put_contents($tmpFilename, $file->saveToString());

        // Download or upload.
        if (!$isUpload) {
            // Prompt for download.
            return new BinaryFileResponse(
                $tmpFilename,
                200,
                [],
                false,
                ResponseHeaderBag::DISPOSITION_ATTACHMENT
            );
        } else {
            // Send to Commons.
            $url = $this->generateUrl('home', [], UrlGeneratorInterface::ABSOLUTE_URL);
            $comment = "File uploaded using svgtranslate tool ($url). Added translation for $targetLang.";
            $url = $uploader->upload($tmpFilename, $filename, $comment);
            $this->addFlash('upload-complete', $url);
            return $this->redirectToRoute('translate', ['filename' => $filename]);
        }
    }

    /**
     * Shows an error related to the selected file
     *
     * @param string $messageKey
     * @param string $fileName
     * @param mixed[] $errorTemplateParams Additional parameters to pass to the template. 'msg_params' will be used for the message.
     * @return Response
     */
    private function showError(string $messageKey, string $fileName, ?array $errorTemplateParams = []): Response
    {
        $message = $this->renderView(
            'error_message.html.twig',
            array_merge([ 'msg_name' => $messageKey, 'msg_params' => [] ], $errorTemplateParams)
        );
        // Flash the message to show to the user under the search form.
        $this->addFlash('search-errors', (string)$message);
        // Also flash the failed filename so we can populate the search form.
        $this->addFlash('search-redirect', $fileName);

        return $this->redirectToRoute('home');
    }

    /**
     * Throws an exception if the given filename is not of an SVG file.
     * Exceptions are used to unify handling with other places that might encounter this problem.
     *
     * @param string $fileName
     */
    private function assertFileType(string $fileName): void
    {
        $extension = mb_strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ('svg' !== $extension) {
            throw new InvalidFormatException($extension);
        }
    }
}
