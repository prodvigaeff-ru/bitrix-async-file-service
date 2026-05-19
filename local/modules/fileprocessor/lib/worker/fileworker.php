<?php
declare(strict_types=1);

namespace FileProcessor\Worker;

use Bitrix\Main\Type\DateTime;
use FileProcessor\Orm\FileQueueTable;

class FileWorker
{
    public function process(int $fileQueueId): void
    {
        $this->updateStatus($fileQueueId, [
            'STATUS'     => 'processing',
            'UPDATED_AT' => new DateTime(),
        ]);

        try {
            $row = $this->fetchRecord($fileQueueId);
            if (!$row) {
                throw new \RuntimeException("File queue record #{$fileQueueId} not found");
            }

            $fileArray = $this->fetchBitrixFile($row['FILE_ID']);
            if (!$fileArray) {
                throw new \RuntimeException("Bitrix file #{$row['FILE_ID']} not found");
            }

            $this->doProcess($fileArray);

            $this->updateStatus($fileQueueId, [
                'STATUS'     => 'done',
                'UPDATED_AT' => new DateTime(),
            ]);
        } catch (\Throwable $e) {
            $this->updateStatus($fileQueueId, [
                'STATUS'        => 'error',
                'ERROR_MESSAGE' => mb_substr($e->getMessage(), 0, 1000),
                'UPDATED_AT'    => new DateTime(),
            ]);
        }
    }

    protected function updateStatus(int $id, array $fields): void
    {
        FileQueueTable::update($id, $fields);
    }

    protected function fetchRecord(int $id): ?array
    {
        $row = FileQueueTable::getById($id)->fetch();
        return $row ?: null;
    }

    protected function fetchBitrixFile(int $fileId): ?array
    {
        $result = \CFile::GetFileArray($fileId);
        return $result ?: null;
    }

    protected function doProcess(array $fileArray): void
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . $fileArray['SRC'];

        if (!file_exists($path)) {
            throw new \RuntimeException("Physical file not found at: {$path}");
        }

        // Simulate processing workload (replace with real logic: image resize, CSV parse, etc.)
        sleep(2);
    }
}
