<?php

namespace App\Common\Serializer;

use DateTimeInterface;
use Illuminate\Support\Str;

abstract class DTOJsonSerializer
{
    public static function toString($object): string
    {
        return json_encode(self::toArray($object));
    }

    public static function toArray($object, $useSnakeCase = false): array
    {
        $properties = get_object_vars($object);

        if ($useSnakeCase) {
            $properties = self::snakeCaseConverter($properties);
        }

        return array_map(function ($value) {
            return $value instanceof DateTimeInterface ? $value->format('Y-m-d H:i:s') : $value;
        }, $properties);
    }

    private static function snakeCaseConverter(array $properties)
    {
        $snakeCase = [];

        foreach ($properties as $key => $value) {
            $snakeCase[Str::snake($key)] = $value;
        }

        return $snakeCase;
    }
}
