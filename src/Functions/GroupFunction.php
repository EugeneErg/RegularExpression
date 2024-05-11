<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

class GroupFunction extends AbstractStructure implements ChildFunctionInterface
{
    use TraitSetParent;
}
