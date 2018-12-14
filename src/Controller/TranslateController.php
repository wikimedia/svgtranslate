<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Model\Svg\SvgFile;
use App\Model\Title;
use App\Service\FileCache;
use Krinkle\Intuition\Intuition;
use OOUI\ButtonInputWidget;
use OOUI\DropdownInputWidget;
use OOUI\FieldLayout;
use OOUI\HorizontalLayout;
use OOUI\LabelWidget;
use OOUI\TextInputWidget;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class TranslateController extends AbstractController
{

    /**
     * Get the filename parameter from the given Request.
     * @param Request $request Normalized filename.
     * @return string
     */
    protected function getFilename(Request $request):string
    {
        return str_replace('_', ' ', $request->get('filename'));
    }

    /**
     * @Route("/File:{filename<.+>}", name="translate", methods={"GET"})
     */
    public function translate(Request $request, Intuition $intuition, Session $session, FileCache $cache):Response
    {
        // Fetch the SVG from Commons.
        $filename = $this->getFilename($request);
        $fileName = Title::normalize($filename);
        $path = $cache->getPath($fileName);
        $svgFile = new SvgFile($path);

        // Upload and download buttons.
        $downloadButton = new ButtonInputWidget([
            'label' => $intuition->msg('download-button-label'),
            'flags' => [ 'progressive' ],
            'type' => 'submit',
            'framed' => false,
        ]);
        $uploadButton = new ButtonInputWidget([
            'label' => $intuition->msg('upload-button-label'),
            'flags' => [ 'progressive' ],
            'type' => 'submit',
            'icon' => 'logoWikimediaCommons',
        ]);
        if (!$session->get('logged_in_user')) {
            // Only logged in users can upload.
            $uploadButton->setDisabled(true);
        }

        // Source and target language selectors.
        $availableLangs = [
            ['data' => 'fallback', 'label' => $intuition->msg('default-language')],
        ];
        foreach ($svgFile->getSavedLanguagesFiltered()['full'] as $lang) {
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
        $targetLangDefault = $intuition->getLang();
        $cookie = $request->cookies->get('svgtranslate');
        if ($cookie) {
            $cookieValue = json_decode($cookie);
            if (isset($cookieValue->interfaceLang)) {
                $targetLangDefault = $cookieValue->interfaceLang;
            }
        }
        $targetLang = new ButtonInputWidget([
            'label' => $intuition->getLangName($targetLangDefault),
            'value' => $targetLangDefault,
            'classes' => ['target-lang-widget'],
            'indicator' => 'down',
            'infusable' => true,
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

        // Messages.
        $translations = $svgFile->getInFileTranslations();
        $formFields = [];
        foreach ($translations as $nodeId => $langs) {
            $inputWidget = new TextInputWidget([
                'name' => 'translation-field-'.$nodeId,
                'value' => isset($langs[$targetLang->getValue()]) ? $langs[$targetLang->getValue()]['text'] : '',
                'data' => ['nodeId' => $nodeId],
            ]);
            $field = new FieldLayout(
                $inputWidget,
                [
                    'label' => $langs[$sourceLang->getValue()]['text'],
                    'infusable' => true,
                ]
            );
            $formFields[] = $field;
        }

        return $this->render('translate.html.twig', [
            'page_class' => 'translate',
            'title' => $filename,
            'filename' => $filename,
            'fields' => $formFields,
            'download_button' => $downloadButton,
            'upload_button' => $uploadButton,
            'language_selectors' => $languageSelectorsLayout,
            'translations' => $translations,
        ]);
    }

    /**
     * @Route("/File:{filename<.+>}", name="svg", methods={"POST"}))
     */
    public function svg(Request $request, Intuition $intuition, Session $session):Response
    {
        $filename = $this->getFilename($request);
        return $this->redirectToRoute('translate', ['filename' => $filename]);
        // @TODO Modify and return SVG.
    }
}
