<?php

namespace App\Providers;

use App\BoundedContext\AuditLog\Domain\Repository\AuditLogRepository;
use App\BoundedContext\AuditLog\Infrastructure\Outbound\MongoDB\MongoAuditLogRepository;
use Illuminate\Support\ServiceProvider;
use MongoDB\Client;

class MongoDBServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Client::class, function ($app) {
            $config = $app['config']['database.connections.mongodb'];
            
            $uri = sprintf(
                'mongodb://%s:%s@%s:%s/%s',
                $config['username'] ?: '',
                $config['password'] ?: '',
                $config['host'],
                $config['port'],
                $config['database']
            );

            return new Client($uri);
        });

        $this->app->bind(AuditLogRepository::class, MongoAuditLogRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
