<?php

namespace App\Exceptions;

interface IApplicationException
{
    public function getMessage(): string;

    public function getLogLevel(): string;
}
