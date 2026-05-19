<?php
use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;

class fileprocessor extends CModule
{
    public $MODULE_ID          = 'fileprocessor';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';
        $this->MODULE_VERSION      = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME         = GetMessage('FP_MODULE_NAME');
        $this->MODULE_DESCRIPTION  = GetMessage('FP_MODULE_DESCRIPTION');
    }

    public function DoInstall(): bool
    {
        $this->InstallDB();
        ModuleManager::registerModule($this->MODULE_ID);
        return true;
    }

    public function DoUninstall(): bool
    {
        $this->UnInstallDB();
        ModuleManager::unRegisterModule($this->MODULE_ID);
        return true;
    }

    public function InstallDB(): void
    {
        $this->executeSqlFile(__DIR__ . '/db/mysql/install.sql');
    }

    public function UnInstallDB(): void
    {
        $this->executeSqlFile(__DIR__ . '/db/mysql/uninstall.sql');
    }

    private function executeSqlFile(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }
        $connection = Application::getConnection();
        foreach (array_filter(explode(';', file_get_contents($path))) as $query) {
            $connection->query(trim($query));
        }
    }
}
