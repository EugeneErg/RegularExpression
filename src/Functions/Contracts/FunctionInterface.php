<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions\Contracts;

use JsonSerializable;
use Stringable;

interface FunctionInterface extends Stringable, JsonSerializable
{
    public static function fromArray(array $data): static;

    /** @return int<0,max> */
    public function getMinLength(): int;

    /** @return int<0,max>|null */
    public function getMaxLength(): ?int;

    public function generate(string $from, bool $negative): string;

    public function jsonSerialize(): string;

    public function getRoot(): RootFunctionInterface;

    public function __toString(): string;
}
