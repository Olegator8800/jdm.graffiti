<?php

use CMain;
use CModule;
use CDatabase;

Class jdm_graffiti extends CModule
{
    /**
     * @var string
     */
    public $MODULE_ID = 'jdm.graffiti';

    /**
     * @var string
     */
    public $MODULE_VERSION;

    /**
     * @var string
     */
    public $MODULE_VERSION_DATE;

    /**
     * @var string
     */
    public $MODULE_NAME;

    /**
     * @var string
     */
    public $MODULE_DESCRIPTION;

    public $MODULE_CSS;

    public function jdm_graffiti()
    {
        $moduleVersion = require __DIR__.'/version.php';

        $this->MODULE_VERSION = $moduleVersion['version'];
        $this->MODULE_VERSION_DATE = $moduleVersion['version_date'];

        $this->MODULE_NAME = 'jdm граффити (jdm graffiti)';
        $this->MODULE_DESCRIPTION = 'тест';
    }

    /**
     * @return string
     */
    public function getModuleId()
    {
        return $this->MODULE_ID;
    }

    /**
     * @return string
     */
    public function getModulePath()
    {
        $docRoot = $GLOBALS['DOCUMENT_ROOT'];
        $moduleId = $this->getModuleId();

        return "{$docRoot}/bitrix/modules/{$moduleId}";
    }

    /**
     * @return CMain
     */
    protected function getApplication()
    {
        return $GLOBALS['APPLICATION'];
    }

    /**
     * @return CDatabase
     */
    protected function getDatabase()
    {
        return $GLOBALS['DB'];
    }

    /**
     * @param array $arParams
     *
     * @return bool
     */
    public function installDB($arParams = array())
    {
        $db = $this->getDatabase();
        $app = $this->getApplication();
        $moduleId = $this->getModuleId();

        $errors = $db->RunSQLBatch(sprintf('%s/install/db/%s/install.sql', $this->getModulePath(), strtolower($db->type)));

        if ($errors) {
            $app->throwException(implode('<br>', $errors));

            return false;
        }

        RegisterModule($moduleId);

        return true;
    }

    /**
     * @param array $arParams
     *
     * @return bool
     */
    public function unInstallDB($arParams = array())
    {
        $db = $this->getDatabase();
        $app = $this->getApplication();
        $moduleId = $this->getModuleId();

        $errors = $db->RunSQLBatch(sprintf('%s/install/db/%s/uninstall.sql', $this->getModulePath(), strtolower($db->type)));

        if ($errors) {
            $app->throwException(implode('<br>', $errors));

            return false;
        }

        UnRegisterModule($moduleId);

        return true;
    }

    public function installFiles($arParams = array())
    {
        $docRoot = $GLOBALS['DOCUMENT_ROOT'];
        $modulePath = $this->getModulePath();

        CopyDirFiles("{$modulePath}/install/components",
                     "{$docRoot}/bitrix/components", true, true);

        return true;
    }

    public function unInstallFiles()
    {
        DeleteDirFilesEx('/bitrix/components/jdm');

        return true;
    }

    public function doInstall()
    {
        $app = $this->getApplication();
        $stepFile = sprintf('%s/install/step.php', $this->getModulePath());

        $this->installDB();
        $this->installFiles();

        $app->includeAdminFile(sprintf('Устанвока модуля [%s]', $this->getModuleId()), $stepFile);
    }

    public function doUninstall()
    {
        $app = $this->getApplication();
        $unstepFile = sprintf('%s/install/unstep.php', $this->getModulePath());

        $this->unInstallDB();
        $this->unInstallFiles();

        $app->includeAdminFile(sprintf('Удаление модуля [%s]', $this->getModuleId()), $unstepFile);
    }
}
