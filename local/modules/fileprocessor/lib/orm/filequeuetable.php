<?php
declare(strict_types=1);

namespace FileProcessor\Orm;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;

class FileQueueTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'fp_file_queue';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary(true)
                ->configureAutocomplete(true),

            (new IntegerField('FILE_ID'))
                ->configureRequired(true),

            (new StringField('ORIGINAL_NAME'))
                ->configureSize(255),

            (new StringField('STATUS'))
                ->configureSize(20)
                ->configureDefaultValue('pending'),

            (new StringField('ERROR_MESSAGE'))
                ->configureSize(1000),

            (new DatetimeField('CREATED_AT')),

            (new DatetimeField('UPDATED_AT')),
        ];
    }
}
