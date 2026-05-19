<?php
declare(strict_types=1);

namespace FileProcessor\Queue;

use FileProcessor\Config;
use FileProcessor\Worker\FileWorker;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer
{
    public function run(): void
    {
        $cfg = Config::getRabbitMQ();

        $connection = new AMQPStreamConnection(
            $cfg['host'],
            $cfg['port'],
            $cfg['user'],
            $cfg['pass'],
            $cfg['vhost']
        );

        $channel = $connection->channel();
        $channel->queue_declare(Producer::QUEUE_NAME, false, true, false, false);

        // Process one message at a time per worker — enables fair round-robin across multiple workers
        $channel->basic_qos(null, 1, null);

        $worker = new FileWorker();

        $callback = static function (AMQPMessage $msg) use ($worker): void {
            $data        = json_decode($msg->getBody(), true, 512, JSON_THROW_ON_ERROR);
            $fileQueueId = (int)$data['file_queue_id'];

            echo '[' . date('Y-m-d H:i:s') . "] Processing file queue ID: {$fileQueueId}\n";

            $worker->process($fileQueueId);
            $msg->ack();

            echo '[' . date('Y-m-d H:i:s') . "] Done: {$fileQueueId}\n";
        };

        $channel->basic_consume(Producer::QUEUE_NAME, '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
