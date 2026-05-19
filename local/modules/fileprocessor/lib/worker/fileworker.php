<?php
declare(strict_types=1);

namespace FileProcessor\Worker;

use Bitrix\Main\Type\DateTime;
use FileProcessor\Orm\FileQueueTable;

class FileWorker
{
    public function process(int $fileQueueId): void
    {
        FileQueueTable::update($fileQueueId, [
            'STATUS'     => 'processing',
            'UPDATED_AT' => new DateTime(),
        ]);

        try {
            $row = FileQueueTable::getById($fileQueueId)->fetch();
            if (!$row) {
                throw new \RuntimeException("File queue record #{$fileQueueId} not found");
            }

            $fileArray = \CFile::GetFileArray($row['FILE_ID']);
            if (!$fileArray) {
                throw new \RuntimeException("Bitrix file #{$row['FILE_ID']} not found");
            }

            $this->processFile($fileArray);

            FileQueueTable::update($fileQueueId, [
                'STATUS'     => 'done',
                'UPDATED_AT' => new DateTime(),
            ]);
        } catch (\Throwable $e) {
            FileQueueTable::update($fileQueueId, [
                'STATUS'        => 'error',
                'ERROR_MESSAGE' => mb_substr($e->getMessage(), 0, 1000),
                'UPDATED_AT'    => new DateTime(),
            ]);
        }
    }

    private function processFile(array $fileArray): void
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . $fileArray['SRC'];

        if (!file_exists($path)) {
            throw new \RuntimeException("Physical file not found at: {$path}");
        }

        // Simulate processing workload (replace with real logic: image resize, CSV parse, etc.)
        sleep(2);
    }
}
