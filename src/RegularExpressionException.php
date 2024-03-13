<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression;

use Exception;

class RegularExpressionException extends Exception
{
    public function __construct(?string $message = null, ?int $code = null, ?string $file = null, ?int $line = null)
    {
        parent::__construct($message ?? preg_last_error_msg(), $code ?? preg_last_error());

        if ($file !== null) {
            $this->file = $file;
        }

        if ($line !== null) {
            $this->line = $line;
        }
    }
}