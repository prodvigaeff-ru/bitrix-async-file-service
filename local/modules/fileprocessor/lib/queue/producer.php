<?php
declare(strict_types=1);

namespace FileProcessor\Queue;

use FileProcessor\Config;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Producer
{
    public const QUEUE_NAME = 'fp_file_processing';

    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct(?AMQPStreamConnection $connection = null)
    {
        if ($connection === null) {
            $cfg = Config::getRabbitMQ();
            $connection = new AMQPStreamConnection(
                $cfg['host'],
                $cfg['port'],
                $cfg['user'],
                $cfg['pass'],
                $cfg['vhost']
            );
        }

        $this->connection = $connection;
        $this->channel    = $this->connection->channel();
        $this->channel->queue_declare(self::QUEUE_NAME, false, true, false, false);
    }

    public function publish(int $fileQueueId): void
    {
        $body    = json_encode(['file_queue_id' => $fileQueueId], JSON_THROW_ON_ERROR);
        $message = new AMQPMessage(
            $body,
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );
        $this->channel->basic_publish($message, '', self::QUEUE_NAME);
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
