<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Model\Title;
use App\OOUI\SearchWidget;
use Krinkle\Intuition\Intuition;
use OOUI\ActionFieldLayout;
use OOUI\ButtonInputWidget;
use OOUI\FormLayout;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function index(Intuition $intuition):Response
    {
        $searchField = new SearchWidget([
            'placeholder' => $intuition->msg('search-placeholder'),
            'name' => 'filename',
            'id' => 'search-widget',
            'infusable' => true,
        ]);
        $submitButton = new ButtonInputWidget([
            'type' => 'submit',
            'label' => $intuition->msg('translate-button'),
            'flags' =>  ['primary', 'progressive'],
        ]);
        $fieldLayoutOpts = [
            'align' => 'top',
            'label' => $intuition->msg('search-label'),
            'help' => $intuition->msg('search-help'),
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

        return $this->redirectToRoute('translate', ['filename' => Title::normalize($filename)]);
    }
}
