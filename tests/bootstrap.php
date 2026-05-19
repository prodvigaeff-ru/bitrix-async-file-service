<?php
// Stubs must be loaded BEFORE source files so Bitrix classes are defined on include
require_once __DIR__ . '/stubs/BitrixStubs.php';

// Vendor autoload (php-amqplib, phpunit dependencies)
require_once __DIR__ . '/../local/vendor/autoload.php';

// Explicitly require source files — bypasses Composer PSR-4 path issues with custom vendor-dir
require_once __DIR__ . '/../local/modules/fileprocessor/lib/config.php';
require_once __DIR__ . '/../local/modules/fileprocessor/lib/orm/filequeuetable.php';
require_once __DIR__ . '/../local/modules/fileprocessor/lib/queue/producer.php';
require_once __DIR__ . '/../local/modules/fileprocessor/lib/queue/consumer.php';
require_once __DIR__ . '/../local/modules/fileprocessor/lib/worker/fileworker.php';
