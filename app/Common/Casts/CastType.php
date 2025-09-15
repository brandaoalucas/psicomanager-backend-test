<?php

namespace App\Common\Casts;

class CastType
{
    public const IMMUTABLE_DATE     = 'immutable_datetime:Y-m-d';
    public const IMMUTABLE_DATETIME = 'immutable_datetime:Y-m-d H:i:s';
    public const JSON               = 'json';
    public const ARRAY              = 'array';
    public const BOOLEAN            = 'boolean';
}
