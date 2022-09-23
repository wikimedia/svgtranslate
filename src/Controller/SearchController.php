<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Model\Title;
use App\OOUI\SearchWidget;
use Krinkle\Intuition\Intuition;
use OOUI\ActionFieldLayout;
use OOUI\ButtonWidget;
use OOUI\FormLayout;
use OOUI\HtmlSnippet;
use OOUI\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function index(Intuition $intuition, Session $session):Response
    {
        $failedSearchTerm = $session->getFlashBag()->get('search-redirect');
        $searchField = new SearchWidget([
            'placeholder' => $intuition->msg('search-placeholder'),
            'name' => 'filename',
            'id' => 'search-widget',
            'infusable' => true,
            'value' => $failedSearchTerm[0] ?? '',
            'required' => true,
        ]);
        $button = new Tag( 'button' );
        $button->setAttributes(['type' => 'submit']);
        $submitButton = new ButtonWidget([
            'button' => $button,
            'label' => $intuition->msg('translate-button'),
            'flags' =>  ['primary', 'progressive'],
        ]);
        // The flashed search errors are HTML, so we have to turn them into objects that FieldLayout will understand.
        $searchErrors = array_map(
            function ($text) {
                return new HtmlSnippet($text);
            },
            $session->getFlashBag()->get('search-errors')
        );
        $fieldLayoutOpts = [
            'align' => 'top',
            'label' => $intuition->msg('search-label'),
            'help' => $intuition->msg('search-help'),
            'errors' => $searchErrors,
        ];
        $fieldLayout = new ActionFieldLayout($searchField, $submitButton, $fieldLayoutOpts);
        $form = new FormLayout([
            'method' => 'get',
            'action' => $this->generateUrl('search'),
            'items' => [$fieldLayout],
        ]);
        return $this->render('search.html.twig', [
            'page_class' => 'search',
            'form' => $form,
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request):Response
    {
        $filename = $request->get('filename');
        if (!$filename) {
            return $this->redirectToRoute('home');
        }

        // Add a flash message that we can use to track whether the user has arrived
        // on the translate page directly or via search.
        $this->addFlash('search-redirect', $filename);
        return $this->redirectToRoute('translate', ['filename' => Title::normalize($filename)]);
    }
}
