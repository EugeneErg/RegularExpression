<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions\GroupFunction;

enum Type
{
    case Group;
    case Condition;
    case Enum;
}