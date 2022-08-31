<?php
declare(strict_types = 1);

namespace App\Exception;

use DOMNode;
use Exception;

class SvgStructureException extends Exception
{

    /** @var DOMNode */
    private $node;

    public function __construct(string $message, DOMNode $node)
    {
        $this->node = $node;
        $msg = $message
            .' (ID: "'.$this->getClosestId().'"; text: "'.$this->getTextContent().'")';
        parent::__construct($msg);
    }

    /**
     * Get the closest ID to the given element.
     * @return string
     */
    public function getClosestId(): string
    {
        $node = $this->node;
        $id = '';
        // Traverse up the tree to find an ID.
        while (!$id && 'svg' !== $node->nodeName) {
            $id = $node->attributes->getNamedItem('id')->textContent ?? '';
            $node = $node->parentNode;
        }
        return $id;
    }

    /**
     * Get a simplified representation of the text content of the element.
     * @return string
     */
    public function getTextContent(): string {
        return $this->node->textContent;
    }
}
