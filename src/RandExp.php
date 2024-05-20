<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression;

abstract class Token
{
    public function __construct(
        public string $type,
        public mixed  $value = null
    )
    {
    }
}

class RootToken extends Token
{
    public function __construct(
        public array $stack
    )
    {
        parent::__construct('ROOT');
    }
}

class GroupToken extends Token
{
    public function __construct(
        public array $options = [],
        public ?bool $remember = null,
        public ?int  $groupNumber = null
    )
    {
        parent::__construct('GROUP');
    }
}

class PositionToken extends Token
{
    public function __construct(
        public string $position
    )
    {
        parent::__construct('POSITION', $position);
    }
}

class SetToken extends Token
{
    public function __construct(
        public array $set,
        public bool  $negate = false
    )
    {
        parent::__construct('SET');
    }
}

class RepetitionToken extends Token
{
    public function __construct(
        public Token $value,
        public int   $min,
        public int   $max
    )
    {
        parent::__construct('REPETITION');
    }
}

class ReferenceToken extends Token
{
    public function __construct(
        public int $value
    )
    {
        parent::__construct('REFERENCE', $value);
    }
}

class CharToken extends Token
{
    public function __construct(
        public int $value
    )
    {
        parent::__construct('CHAR', $value);
    }
}


class RandExp implements Stringable
{
    private bool $ignoreCase;
    private bool $multiline;
    private DiscontinuousRange $defaultRange;
    private array $tokens;
    public int $max = 100;

    public function __construct(string $pattern, string $flags = '')
    {
        $this->defaultRange = new DiscontinuousRange(32, 126);

        $this->ignoreCase = str_contains($flags, 'i');
        $this->multiline = str_contains($flags, 'm');

        $this->tokens = $this->tokenize($pattern);
    }

    public function __toString(): string
    {
        $groups = [];

        return $this->generate($this->tokens, $groups);
    }

    public static function randExp(string $pattern, string $flags = ''): string
    {
        $instance = new self($pattern, $flags);
        return (string) $instance;
    }

    private function generate(array $tokens, array &$groups): string
    {
        $result = '';

        foreach ($tokens as $token) {
            $result .= match ($token->type) {
                'ROOT', 'GROUP' => $this->handleGroup($token, $groups),
                'POSITION' => '',
                'SET' => $this->handleSet($token),
                'REPETITION' => $this->handleRepetition($token, $groups),
                'REFERENCE' => $groups[$token->value - 1] ?? '',
                'CHAR' => $this->handleChar($token),
                default => throw new InvalidArgumentException("Unknown token type: {$token->type}"),
            };
        }

        return $result;
    }

    private function handleGroup(Token $token, array &$groups): string
    {
        if ($token instanceof GroupToken) {
            if (isset($token->followedBy) || isset($token->notFollowedBy)) {
                return '';
            }

            if (isset($token->remember) && !isset($token->groupNumber)) {
                $token->groupNumber = array_push($groups, null) - 1;
            }

            $result = '';
            $stack = $token->options ?? $token->stack;

            foreach ($stack as $subToken) {
                $result .= $this->generate([$subToken], $groups);
            }

            if (isset($token->remember)) {
                $groups[$token->groupNumber] = $result;
            }

            return $result;
        }
        throw new InvalidArgumentException('Invalid group token');
    }

    private function handleSet(Token $token): string
    {
        if ($token instanceof SetToken) {
            $set = $this->expand($token);
            return chr($this->choose($set));
        }
        throw new InvalidArgumentException('Invalid set token');
    }

    private function handleRepetition(Token $token, array &$groups): string
    {
        if ($token instanceof RepetitionToken) {
            $reps = $this->randInt($token->min, $token->max === INF ? $token->min + $this->max : $token->max);
            $result = '';

            for ($i = 0; $i < $reps; $i++) {
                $result .= $this->generate([$token->value], $groups);
            }

            return $result;
        }
        throw new InvalidArgumentException('Invalid repetition token');
    }

    private function handleChar(Token $token): string
    {
        if ($token instanceof CharToken) {
            $char = $token->value;
            if ($this->ignoreCase && $this->bool()) {
                $char = $this->toggleCase($char);
            }
            return chr($char);
        }
        throw new InvalidArgumentException('Invalid char token');
    }

    private function bool(): bool
    {
        return rand(0, 1) === 0;
    }

    private function randInt(int $min, int $max): int
    {
        return $min + mt_rand(0, $max - $min);
    }

    private function choose(array $array): int
    {
        return $array[$this->randInt(0, count($array) - 1)];
    }

    private function expand(SetToken $token): array
    {
        // Implementation of expanding token based on its type.
        // This is simplified and should match your JavaScript logic.
    }

    private function toggleCase(int $char): int
    {
        return match ($char) {
            97. . .122 => $char - 32,
            65. . .90 => $char + 32,
            default => $char,
        };
    }

    private function tokenize(string $pattern): array
    {
        // Implementation of tokenizing the pattern.
        // This should be a port of the JS tokenize function.
        // Return an array of Token objects.
    }
}

class DiscontinuousRange
{
    private array $ranges = [];
    public int $length = 0;

    public function __construct(?int $start = null, ?int $end = null)
    {
        if ($start !== null) {
            $this->add($start, $end);
        }
    }

    public function add(int $start, ?int $end = null): void
    {
        // Add range logic
    }

    public function subtract(int $start, ?int $end = null): void
    {
        // Subtract range logic
    }

    public function index(int $index): mixed
    {
        // Indexing logic
    }

    public function clone(): self
    {
        // Clone logic
    }
}
