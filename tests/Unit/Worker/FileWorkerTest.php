<?php
declare(strict_types=1);

namespace FileProcessor\Tests\Unit\Worker;

use FileProcessor\Worker\FileWorker;
use PHPUnit\Framework\TestCase;

class FileWorkerTest extends TestCase
{
    public function testSuccessfulProcessingSetsStatusDone(): void
    {
        $worker = $this->buildWorker(
            record:    ['FILE_ID' => 1, 'ORIGINAL_NAME' => 'test.txt'],
            fileArray: ['SRC' => '/upload/test.txt']
        );

        $worker->process(1);

        $this->assertCount(2, $worker->capturedUpdates);
        $this->assertSame('processing', $worker->capturedUpdates[0]['STATUS']);
        $this->assertSame('done', $worker->capturedUpdates[1]['STATUS']);
    }

    public function testMissingRecordSetsErrorStatus(): void
    {
        $worker = $this->buildWorker(record: null, fileArray: null);

        $worker->process(99);

        $this->assertSame('processing', $worker->capturedUpdates[0]['STATUS']);
        $this->assertSame('error', $worker->capturedUpdates[1]['STATUS']);
        $this->assertStringContainsString('#99', $worker->capturedUpdates[1]['ERROR_MESSAGE']);
    }

    public function testMissingBitrixFileSetsErrorStatus(): void
    {
        $worker = $this->buildWorker(record: ['FILE_ID' => 5], fileArray: null);

        $worker->process(1);

        $this->assertSame('error', $worker->capturedUpdates[1]['STATUS']);
        $this->assertStringContainsString('#5', $worker->capturedUpdates[1]['ERROR_MESSAGE']);
    }

    public function testErrorMessageIsTruncatedTo1000Chars(): void
    {
        $worker = new class extends FileWorker {
            public array $capturedUpdates = [];

            protected function updateStatus(int $id, array $fields): void { $this->capturedUpdates[] = $fields; }
            protected function fetchRecord(int $id): ?array                { return ['FILE_ID' => 1]; }
            protected function fetchBitrixFile(int $fileId): ?array        { return ['SRC' => '/f']; }
            protected function doProcess(array $fileArray): void           { throw new \RuntimeException(str_repeat('x', 2000)); }
        };

        $worker->process(1);

        $this->assertLessThanOrEqual(1000, mb_strlen($worker->capturedUpdates[1]['ERROR_MESSAGE']));
    }

    private function buildWorker(?array $record, ?array $fileArray): FileWorker
    {
        return new class($record, $fileArray) extends FileWorker {
            public array $capturedUpdates = [];

            public function __construct(
                private readonly ?array $record,
                private readonly ?array $fileArray
            ) {}

            protected function updateStatus(int $id, array $fields): void { $this->capturedUpdates[] = $fields; }
            protected function fetchRecord(int $id): ?array                { return $this->record; }
            protected function fetchBitrixFile(int $fileId): ?array        { return $this->fileArray; }
            protected function doProcess(array $fileArray): void           {}
        };
    }
}
