<?php
declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use FileProcessor\Orm\FileQueueTable;

class FileListComponent extends \CBitrixComponent
{
    public function executeComponent(): void
    {
        Loader::includeModule('fileprocessor');

        $query = FileQueueTable::getList([
            'order'  => ['CREATED_AT' => 'DESC'],
            'select' => ['ID', 'FILE_ID', 'ORIGINAL_NAME', 'STATUS', 'ERROR_MESSAGE', 'CREATED_AT'],
        ]);

        $files = [];
        while ($row = $query->fetch()) {
            $fileArray          = \CFile::GetFileArray($row['FILE_ID']);
            $row['DOWNLOAD_URL'] = $fileArray ? \CFile::GetFileSRC($fileArray) : null;
            $files[]            = $row;
        }

        $this->arResult['FILES'] = $files;

        $this->includeComponentTemplate();
    }
}
