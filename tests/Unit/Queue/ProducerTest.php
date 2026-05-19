<?php
declare(strict_types=1);

namespace FileProcessor\Tests\Unit\Queue;

use FileProcessor\Queue\Producer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

class ProducerTest extends TestCase
{
    public function testQueueName(): void
    {
        $this->assertSame('fp_file_processing', Producer::QUEUE_NAME);
    }

    public function testPublishSendsMessageToCorrectQueue(): void
    {
        [$producer, $channel] = $this->makeProducer();

        $channel->expects($this->once())
            ->method('basic_publish')
            ->with(
                $this->isInstanceOf(AMQPMessage::class),
                '',
                Producer::QUEUE_NAME
            );

        $producer->publish(42);
    }

    public function testPublishEncodesFileQueueId(): void
    {
        [$producer, $channel] = $this->makeProducer();

        $captured = null;
        $channel->method('basic_publish')
            ->willReturnCallback(static function (AMQPMessage $msg) use (&$captured): void {
                $captured = json_decode($msg->getBody(), true);
            });

        $producer->publish(99);

        $this->assertSame(99, $captured['file_queue_id']);
    }

    public function testPublishUsesPersistentDeliveryMode(): void
    {
        [$producer, $channel] = $this->makeProducer();

        $captured = null;
        $channel->method('basic_publish')
            ->willReturnCallback(static function (AMQPMessage $msg) use (&$captured): void {
                $captured = $msg;
            });

        $producer->publish(1);

        $this->assertSame(
            AMQPMessage::DELIVERY_MODE_PERSISTENT,
            $captured->get('delivery_mode')
        );
    }

    /** @return array{Producer, AMQPChannel&\PHPUnit\Framework\MockObject\MockObject} */
    private function makeProducer(): array
    {
        $channel = $this->createMock(AMQPChannel::class);
        $channel->method('queue_declare');

        $connection = $this->createMock(AMQPStreamConnection::class);
        $connection->method('channel')->willReturn($channel);

        return [new Producer($connection), $channel];
    }
}
