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

    /**
     * Default error code - should be overridden by specific exceptions
     */
    public function getErrorCode(): string
    {
        return 'APPLICATION_ERROR';
    }

    /**
     * Default HTTP code - should be overridden by specific exceptions
     */
    public function getHttpCode(): int
    {
        return 400; // Bad Request as default
    }
}
