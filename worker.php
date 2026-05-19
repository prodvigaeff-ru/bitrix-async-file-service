<?php
declare(strict_types=1);

/**
 * RabbitMQ consumer entry point.
 * Run: php worker.php
 * Scale: php worker.php & php worker.php & php worker.php &
 */

$_SERVER['DOCUMENT_ROOT'] = __DIR__;

define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);

require_once __DIR__ . '/bitrix/modules/main/include/prolog_before.php';
require_once __DIR__ . '/local/vendor/autoload.php';

\Bitrix\Main\Loader::includeModule('fileprocessor');

$consumer = new \FileProcessor\Queue\Consumer();

echo '[' . date('Y-m-d H:i:s') . "] Worker started. Waiting for messages...\n";

$consumer->run();
