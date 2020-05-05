<?php
declare(strict_types = 1);

namespace App\Exception;

use DOMElement;
use Exception;

class NestedTspanException extends Exception
{

    /** @var DOMElement */
    protected $tspan;

    public function __construct(DOMElement $tspan)
    {
        $this->tspan = $tspan;
        $msg = 'Nested tspan elements are not supported '
            .'(ID: "'.$this->getTspanId().'"; text: "'.$this->getTextContent().'")';
        parent::__construct($msg);
    }

    /**
     * Get the closest ID to the nested tspan.
     * @return string
     */
    public function getTspanId(): string
    {
        $el = $this->tspan;
        $id = '';
        // Traverse up the tree to find an ID.
        while (!$id && 'svg' !== $el->nodeName) {
            $id = $el->getAttribute('id');
            $el = $el->parentNode;
        }
        return $id;
    }

    public function getTextContent(): string
    {
        return $this->tspan->textContent;
    }
}
