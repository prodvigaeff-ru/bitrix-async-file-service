<?php
declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use FileProcessor\Orm\FileQueueTable;
use FileProcessor\Queue\Producer;

class FileUploadComponent extends \CBitrixComponent
{
    public function executeComponent(): void
    {
        Loader::includeModule('fileprocessor');

        $this->arResult['ERROR']   = '';
        $this->arResult['SUCCESS'] = false;

        $request = Application::getInstance()->getContext()->getRequest();

        if ($request->isPost() && check_bitrix_sessid()) {
            $this->handleUpload($request);
        }

        $this->includeComponentTemplate();
    }

    private function handleUpload(\Bitrix\Main\HttpRequest $request): void
    {
        $file = $request->getFile('upload_file');

        if (empty($file) || (int)$file['error'] !== UPLOAD_ERR_OK) {
            $this->arResult['ERROR'] = 'Файл не выбран или произошла ошибка при загрузке';
            return;
        }

        $fileId = \CFile::SaveFile(
            [
                'name'     => $file['name'],
                'type'     => $file['type'],
                'tmp_name' => $file['tmp_name'],
                'error'    => $file['error'],
                'size'     => $file['size'],
            ],
            'fileprocessor'
        );

        if (!$fileId) {
            $this->arResult['ERROR'] = 'Не удалось сохранить файл в системе';
            return;
        }

        $now    = new DateTime();
        $result = FileQueueTable::add([
            'FILE_ID'       => $fileId,
            'ORIGINAL_NAME' => $file['name'],
            'STATUS'        => 'pending',
            'CREATED_AT'    => $now,
            'UPDATED_AT'    => $now,
        ]);

        if (!$result->isSuccess()) {
            \CFile::Delete($fileId);
            $this->arResult['ERROR'] = implode(', ', $result->getErrorMessages());
            return;
        }

        try {
            $producer = new Producer();
            $producer->publish($result->getId());
        } catch (\Throwable $e) {
            $this->arResult['ERROR'] = 'Файл сохранён, но не удалось поставить задачу в очередь: ' . $e->getMessage();
            return;
        }

        $this->arResult['SUCCESS'] = true;
    }
}
