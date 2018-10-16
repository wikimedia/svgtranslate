<?php
declare(strict_types = 1);

namespace App\OOUI;

use OOUI\SearchInputWidget;

class SearchWidget extends SearchInputWidget
{

    /**
     * The class name of the JavaScript version of this widget
     * @return string
     */
    protected function getJavaScriptClassName(): string
    {
        return 'App.SearchWidget';
    }
}
