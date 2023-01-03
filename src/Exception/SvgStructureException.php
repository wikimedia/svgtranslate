<?php

declare(strict_types = 1);

namespace App\Exception;

use DOMNode;
use Exception;

class SvgStructureException extends Exception
{

    /** @var DOMNode|null */
    private $node;

    /** @var string[] */
    private $messageParams;

    /**
     * @param string $message The i18n message name.
     * @param DOMNode|null $node The DOM node of interest to this exception. The ID and text contents of this node may be shown to the user.
     * @param string[] $messageParams Extra message parameters. Parameter 1 is always the closes element ID (if found).
     */
    public function __construct(string $message, ?DOMNode $node = null, array $messageParams = [])
    {
        parent::__construct($message);
        $this->node = $node;
        $this->messageParams = $messageParams;
        // Add the ID as parameter $1.
        array_unshift($this->messageParams, $this->getClosestId());
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
        while (!$id && $node && 'svg' !== $node->nodeName) {
            $id = $node->attributes && $node->attributes->getNamedItem('id')
                ? $node->attributes->getNamedItem('id')->textContent
                : '';
            $node = $node->parentNode;
        }
        return $id;
    }

    /**
     * Get a simplified representation of the text content of the element.
     * @return string
     */
    public function getTextContent(): string
    {
        return $this->node->textContent ?? '';
    }

    /**
     * Get the parameters to pass to the i18n message.
     * @return string[]
     */
    public function getMessageParams(): array
    {
        return $this->messageParams;
    }
}
