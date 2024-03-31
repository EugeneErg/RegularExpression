<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

final class CustomFunction implements FunctionInterface
{
    private array $children;
    private ?CustomFunction $parent;
    private array $options;

    public function __construct(public readonly string $name)
    {
    }

    public function addChild(string|CustomFunction ...$children): void
    {
        $lastKey = $this->getLastKey();
        $lastChildIsString = is_string($this->children[$lastKey]);

        foreach ($children as $child) {
            if ($child instanceof CustomFunction) {
                $this->children[] = $child;
                $lastKey++;
                $lastChildIsString = false;
                $child->setParent($this);
            } elseif ($lastChildIsString) {
                $this->children[$lastKey] .= $child;
            } else {
                $this->children[] = $child;
                $lastKey++;
                $lastChildIsString = true;
            }
        }
    }

    public function setParent(?CustomFunction $parent): void
    {
        $this->parent = $parent;
    }

    public function __toString(): string
    {
        return preg_quote($this->name) . implode(',', array_map(
            fn (string|CustomFunction $value): string => is_string($value) ? preg_quote($value) : (string) $value,
            $this->children,
        ));
    }

    public function getChildren(): Functions
    {
        return new Functions(...$this->children);
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'values' => $this->children,
        ];
    }

    public function getParent(): ?CustomFunction
    {
        return $this->parent;
    }

    public function getLastChild(): null|string|CustomFunction
    {
        $result = end($this->children);

        return $result === false ? null : $result;
    }

    public function getLastKey(): ?int
    {
        $result = count($this->children);

        return $result < 1 ? null : $result - 1;
    }

    public function replaceLastChild(string|FunctionInterface $value): void
    {
        $this->children[$this->getLastKey()] = $value;

        if ($value instanceof CustomFunction) {
            $value->setParent($this);
        }
    }

    public function setOption(mixed ...$value): self
    {
        $this->options = array_replace($this->options, $value);

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}