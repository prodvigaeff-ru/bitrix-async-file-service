<?php
declare(strict_types=1);

namespace FileProcessor;

class Config
{
    public static function getRabbitMQ(): array
    {
        return [
            'host'  => (string)(getenv('RABBITMQ_HOST') ?: 'localhost'),
            'port'  => (int)(getenv('RABBITMQ_PORT') ?: 5672),
            'user'  => (string)(getenv('RABBITMQ_USER') ?: 'guest'),
            'pass'  => (string)(getenv('RABBITMQ_PASS') ?: 'guest'),
            'vhost' => (string)(getenv('RABBITMQ_VHOST') ?: '/'),
        ];
    }
}
