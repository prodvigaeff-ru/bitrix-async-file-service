<?php
declare(strict_types=1);

namespace FileProcessor\Tests\Unit;

use FileProcessor\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    protected function tearDown(): void
    {
        putenv('RABBITMQ_HOST');
        putenv('RABBITMQ_PORT');
        putenv('RABBITMQ_USER');
        putenv('RABBITMQ_PASS');
        putenv('RABBITMQ_VHOST');
    }

    public function testDefaultValues(): void
    {
        $cfg = Config::getRabbitMQ();

        $this->assertSame('localhost', $cfg['host']);
        $this->assertSame(5672, $cfg['port']);
        $this->assertSame('guest', $cfg['user']);
        $this->assertSame('guest', $cfg['pass']);
        $this->assertSame('/', $cfg['vhost']);
    }

    public function testEnvOverridesDefaults(): void
    {
        putenv('RABBITMQ_HOST=myrabbit');
        putenv('RABBITMQ_PORT=5673');
        putenv('RABBITMQ_USER=admin');
        putenv('RABBITMQ_PASS=secret');
        putenv('RABBITMQ_VHOST=/myapp');

        $cfg = Config::getRabbitMQ();

        $this->assertSame('myrabbit', $cfg['host']);
        $this->assertSame(5673, $cfg['port']);
        $this->assertSame('admin', $cfg['user']);
        $this->assertSame('secret', $cfg['pass']);
        $this->assertSame('/myapp', $cfg['vhost']);
    }

    public function testAllKeysArePresent(): void
    {
        $cfg = Config::getRabbitMQ();

        $this->assertArrayHasKey('host', $cfg);
        $this->assertArrayHasKey('port', $cfg);
        $this->assertArrayHasKey('user', $cfg);
        $this->assertArrayHasKey('pass', $cfg);
        $this->assertArrayHasKey('vhost', $cfg);
    }

    public function testPortIsCastToInt(): void
    {
        putenv('RABBITMQ_PORT=5673');

        $cfg = Config::getRabbitMQ();

        $this->assertIsInt($cfg['port']);
    }
}
