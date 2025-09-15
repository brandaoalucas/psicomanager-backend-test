<?php

namespace App\Exceptions;

interface IApplicationException
{
    public function getMessage(): string;

    public function getLogLevel(): string;

    /**
     * Get standardized error code for API responses
     */
    public function getErrorCode(): string;

    /**
     * Get appropriate HTTP status code for this exception
     */
    public function getHttpCode(): int;
}
