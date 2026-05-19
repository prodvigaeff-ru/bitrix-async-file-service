<?php
declare(strict_types=1);

namespace FileProcessor\Tests\Unit\Worker;

use FileProcessor\Worker\FileWorker;
use PHPUnit\Framework\TestCase;

class FileWorkerTest extends TestCase
{
    public function testSuccessfulProcessingSetsStatusDone(): void
    {
        $updates = [];
        $worker  = $this->makeWorker(
            record:    ['FILE_ID' => 1, 'ORIGINAL_NAME' => 'test.txt'],
            fileArray: ['SRC' => '/upload/test.txt'],
            updates:   $updates
        );

        $worker->process(1);

        $this->assertCount(2, $updates);
        $this->assertSame('processing', $updates[0]['STATUS']);
        $this->assertSame('done', $updates[1]['STATUS']);
    }

    public function testMissingRecordSetsErrorStatus(): void
    {
        $updates = [];
        $worker  = $this->makeWorker(record: null, fileArray: null, updates: $updates);

        $worker->process(99);

        $this->assertSame('processing', $updates[0]['STATUS']);
        $this->assertSame('error', $updates[1]['STATUS']);
        $this->assertStringContainsString('#99', $updates[1]['ERROR_MESSAGE']);
    }

    public function testMissingBitrixFileSetsErrorStatus(): void
    {
        $updates = [];
        $worker  = $this->makeWorker(
            record:    ['FILE_ID' => 5],
            fileArray: null,
            updates:   $updates
        );

        $worker->process(1);

        $this->assertSame('error', $updates[1]['STATUS']);
        $this->assertStringContainsString('#5', $updates[1]['ERROR_MESSAGE']);
    }

    public function testErrorMessageIsTruncatedTo1000Chars(): void
    {
        $updates = [];
        $worker  = new class($updates) extends FileWorker {
            public function __construct(private array &$updates) {}

            protected function updateStatus(int $id, array $fields): void { $this->updates[] = $fields; }
            protected function fetchRecord(int $id): ?array                { return ['FILE_ID' => 1]; }
            protected function fetchBitrixFile(int $fileId): ?array        { return ['SRC' => '/f']; }

            protected function doProcess(array $fileArray): void
            {
                throw new \RuntimeException(str_repeat('x', 2000));
            }
        };

        $worker->process(1);

        $this->assertLessThanOrEqual(1000, mb_strlen($updates[1]['ERROR_MESSAGE']));
    }

    private function makeWorker(?array $record, ?array $fileArray, array &$updates): FileWorker
    {
        return new class($record, $fileArray, $updates) extends FileWorker {
            public function __construct(
                private readonly ?array $record,
                private readonly ?array $fileArray,
                private array &$updates
            ) {}

            protected function updateStatus(int $id, array $fields): void { $this->updates[] = $fields; }
            protected function fetchRecord(int $id): ?array                { return $this->record; }
            protected function fetchBitrixFile(int $fileId): ?array        { return $this->fileArray; }
            protected function doProcess(array $fileArray): void           {}
        };
    }
}
