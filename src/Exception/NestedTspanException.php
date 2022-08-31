<?php
declare(strict_types = 1);

namespace App\Exception;

use DOMElement;

class NestedTspanException extends SvgStructureException
{

    public function __construct(DOMElement $tspan)
    {
        parent::__construct('Nested tspan elements are not supported', $tspan);
    }
}
