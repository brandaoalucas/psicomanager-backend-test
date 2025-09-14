<?php

namespace App\Exceptions;

use Exception;
use Throwable;

abstract class AbstractIApplicationException extends Exception implements IApplicationException
{
    private array $args;

    public function __construct(array $args = [], ?Throwable $previous = null)
    {
        parent::__construct(
            str_replacements($this->message(), $args),
            0,
            $previous,
        );

        $this->args = $args;
    }

    abstract protected function message(): string;

    public function getLogLevel(): string
    {
        return 'warning';
    }

    public function getArgs(): array
    {
        return $this->args;
    }
}
