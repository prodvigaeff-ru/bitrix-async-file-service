<?php
declare(strict_types=1);

namespace FileProcessor\Tests\Unit\Orm;

use FileProcessor\Orm\FileQueueTable;
use PHPUnit\Framework\TestCase;

class FileQueueTableTest extends TestCase
{
    public function testTableName(): void
    {
        $this->assertSame('fp_file_queue', FileQueueTable::getTableName());
    }

    public function testMapHasSevenFields(): void
    {
        $this->assertCount(7, FileQueueTable::getMap());
    }

    public function testMapContainsRequiredFields(): void
    {
        $names = array_map(
            static fn($field) => $field->getName(),
            FileQueueTable::getMap()
        );

        $this->assertContains('ID', $names);
        $this->assertContains('FILE_ID', $names);
        $this->assertContains('ORIGINAL_NAME', $names);
        $this->assertContains('STATUS', $names);
        $this->assertContains('ERROR_MESSAGE', $names);
        $this->assertContains('CREATED_AT', $names);
        $this->assertContains('UPDATED_AT', $names);
    }

    public function testStatusDefaultIsPending(): void
    {
        foreach (FileQueueTable::getMap() as $field) {
            if ($field->getName() === 'STATUS') {
                $this->assertSame('pending', $field->getDefaultValue());
                return;
            }
        }
        $this->fail('STATUS field not found in map');
    }
}
