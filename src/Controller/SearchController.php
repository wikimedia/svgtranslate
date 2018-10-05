<?php
declare(strict_types = 1);

namespace App\Controller;

use Krinkle\Intuition\Intuition;
use OOUI\ActionFieldLayout;
use OOUI\ButtonInputWidget;
use OOUI\FormLayout;
use OOUI\TextInputWidget;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function index(Intuition $intuition):Response
    {
        $searchField = new TextInputWidget([
            'placeholder' => $intuition->msg('search-placeholder'),
            'name' => 'q',
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
            'form' => $form,
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search():Response
    {
        return $this->render('search.html.twig', [
            'form' => '',
        ]);
    }
}
