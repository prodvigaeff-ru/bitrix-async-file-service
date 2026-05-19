<?php
use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses('fileprocessor', [
    'FileProcessor\\Config'              => 'lib/config.php',
    'FileProcessor\\Orm\\FileQueueTable' => 'lib/orm/filequeuetable.php',
    'FileProcessor\\Queue\\Producer'     => 'lib/queue/producer.php',
    'FileProcessor\\Queue\\Consumer'     => 'lib/queue/consumer.php',
    'FileProcessor\\Worker\\FileWorker'  => 'lib/worker/fileworker.php',
]);
