<?php

namespace App\Helpers\Logger;

use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * @method static void started(array $context = [])
 * @method static void finished(array $context = [])
 *
 * @method static void debug(array $context = [])
 * @method static void error(array $context = [], ?Throwable $exception = null)
 * @method static void warning(array $context = [], ?Throwable $exception = null)
 */
class Logger
{
    public static function __callStatic(string $name, array $arguments): void
    {
        self::log($name, data_get($arguments, 0, []), data_get($arguments, 1));
    }

    public static function status(string $status, array $arguments = []): void
    {
        self::log($status, $arguments);
    }

    private static function log(string $status, array $context, ?Throwable $exception = null): void
    {
        $backtrace  = debug_backtrace(limit: 3)[2];
        $callerName = sprintf("%s.%s", data_get($backtrace, 'class'), data_get($backtrace, 'function'));

        $params = [
            sprintf(
                "process=%s, status=%s",
                last(explode("\\", $callerName)),
                last(explode("::", $status)),
            ),
            ['context' => json_encode(self::createSerializableContext($context, $exception))]
        ];

        forward_static_call_array(
            [Log::class, self::getLogMethod($status)],
            $params,
        );
    }

    private static function createSerializableContext(array $context, ?Throwable $exception = null): array
    {
        foreach ($context as $key => $value) {
            $context[$key] = stringify($value);
        }

        if ($exception) {
            $context['error_message']    = $exception->getMessage();
            $context['error_stacktrace'] = $exception->getTraceAsString();
        }

        return $context;
    }

    private static function getLogMethod(string $status): string
    {
        return match ($status) {
            "error" => "error",
            "debug" => "debug",
            default => "info",
        };
    }
}
