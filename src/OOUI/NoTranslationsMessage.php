<?php
declare(strict_types = 1);

namespace App\OOUI;

use Krinkle\Intuition\Intuition;
use OOUI\ButtonWidget;
use OOUI\Tag;

class NoTranslationsMessage extends Tag
{

    /** @var Intuition */
    protected $intuition;

    public function __construct(string $tag = 'span')
    {
        parent::__construct($tag);
    }

    public function setIntuition(Intuition $intuition): void
    {
        $this->intuition = $intuition;
    }

    /**
     * Add the message, classes, and a help-link button, and return the HTML.
     * @return string
     */
    public function toString(): string
    {
        $this->addClasses(['no-translations-message']);
        $helpButton = new ButtonWidget([
            'href' => 'https://commons.wikimedia.org/wiki/Special:MyLanguage/Commons:SVG_Translate_tool#FAQ',
            'icon' => 'helpNotice',
            'framed' => false,
        ]);
        $this->appendContent([
            $this->intuition->msg('no-translations'),
            ' ',
            $helpButton,
        ]) ;
        return parent::toString();
    }
}
