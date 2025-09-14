<?php

namespace App\BoundedContext\Auth\Application\Service;

use Illuminate\Support\Facades\RateLimiter;

class LoginAttemptService
{
    protected static function key(string $ip): string
    {
        return 'login:attempt:'.$ip;
    }

    public static function increment(string $ip): void
    {
        RateLimiter::hit(
            key: self::key($ip),
            decaySeconds: config('auth.login.decay_seconds')
        );
    }

    public static function clear(string $ip): void
    {
        RateLimiter::clear(key: self::key($ip));
    }

    public static function isBlockedAttempt(string $ip): bool
    {
        return RateLimiter::tooManyAttempts(
            key: self::key($ip),
            maxAttempts: config('auth.login.max_attempts', 5) + 1
        );
    }

    public static function checkTooManyFailedAttempts(string $ip): bool
    {
        return RateLimiter::tooManyAttempts(
            key: self::key($ip),
            maxAttempts: config('auth.login.max_attempts', 5)
        );
    }
}
