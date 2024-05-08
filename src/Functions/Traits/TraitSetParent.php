<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions\Traits;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Contracts\ParentFunctionInterface;
use EugeneErg\RegularExpression\Functions\Contracts\RootFunctionInterface;

/**
 * @mixin ChildFunctionInterface
 */
trait TraitSetParent
{
    public readonly ParentFunctionInterface $parent;
    public readonly RootFunctionInterface $root;

    public function getParent(): ParentFunctionInterface
    {
        return $this->parent;
    }

    public function getRoot(): RootFunctionInterface
    {
        return $this->root;
    }

    public function setParent(ParentFunctionInterface $parent): void
    {
        $this->parent = $parent;
        $this->root = $parent->getRoot();
    }
}