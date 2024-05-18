<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions\Traits;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Contracts\ParentFunctionInterface;

/**
 * @mixin ParentFunctionInterface
 */
trait TraitSetChildren
{
    /** @var ChildFunctionInterface[] */
    private array $children;

    /** @return ChildFunctionInterface[] */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChild(ChildFunctionInterface $child): void
    {
        $this->children[] = $child;
    }
}
