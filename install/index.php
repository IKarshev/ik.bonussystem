<?
use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\Config\Option;

use Ik\BonusSystem\OrderPropertys;

IncludeModuleLangFile(__FILE__);

// Orm
use Ik\BonusSystem\Orm\BonusTable;

Class Ik_Bonussystem extends CModule
{
    var $MODULE_ID = "ik.bonussystem";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $errors;

    function __construct()
    {
        $this->MODULE_VERSION = "0.0.1";
        $this->MODULE_VERSION_DATE = "22.09.2024";
        $this->MODULE_NAME = "Бонусная система";
        $this->MODULE_DESCRIPTION = "Накопительная бонусная система";
        $this->PARTNER_NAME = "IvanKarshev";
		$this->PARTNER_URI = "https://github.com/IKarshev";

        $this->eventManager = EventManager::getInstance();
    }

    function DoInstall()
    {
        global $APPLICATION;

        RegisterModule($this->MODULE_ID);

        $this->InstallDB();
        $this->InstallEvents();
        $this->InstallFiles();
        $this->InstallPropertys();

        $APPLICATION->includeAdminFile(
            "Установочное сообщение",
            __DIR__ . '/instalInfo.php'
        );
        return true;
    }

    function DoUninstall()
    {
        global $APPLICATION;

        $this->UnInstallDB();
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        $this->UnInstallPropertys();

        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->includeAdminFile(
            "Сообщение деинсталяции",
            __DIR__ . '/deInstalInfo.php'
        );
        return true;
    }

    function InstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        if (!Application::getConnection()->isTableExists(BonusTable::getTableName())) {
            BonusTable::getEntity()->createDbTable();
        };
        return true;
    }
    
    function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        if (Application::getConnection()->isTableExists(BonusTable::getTableName())) {
            Application::getConnection()->dropTable(BonusTable::getTableName());
        }
        return true;
    }

    function InstallEvents()
    {
        $this->eventManager->registerEventHandler(
            'sale',
            'OnSaleOrderBeforeSaved',
            $this->MODULE_ID,
            '\\Ik\\BonusSystem\\EventHandler',
            'OnSaleOrderBeforeSavedHandler',
        );

        $this->eventManager->registerEventHandler(
            'sale',
            'OnSaleCancelOrder',
            $this->MODULE_ID,
            '\\Ik\\BonusSystem\\EventHandler',
            'OnSaleCancelOrderHandler'
        );
    }

    function UnInstallEvents()
    {
        $this->eventManager->unRegisterEventHandler(
            'sale',
            'OnSaleOrderBeforeSaved',
            $this->MODULE_ID,
            '\\Ik\\BonusSystem\\EventHandler',
            'OnSaleOrderBeforeSavedHandler',
        );

        $this->eventManager->unRegisterEventHandler(
            "sale",
            "OnSaleCancelOrder",
            $this->MODULE_ID,
            '\\Ik\\BonusSystem\\EventHandler',
            'OnSaleCancelOrderHandler'
        );
    }

    function InstallPropertys()
    {
        if( Loader::includeModule($this->MODULE_ID) ){
            $properties = new OrderPropertys();
            $properties->createPropertiesBonus();
        }
    }

    function UnInstallPropertys()
    {
        if( Loader::includeModule($this->MODULE_ID) ){
            $properties = new OrderPropertys();
            $properties->deletePropertiesBonus();
        }
    }

    function InstallFiles()
    {
        /*
        CopyDirFiles(
            __DIR__ . '/admin/settings',
            Application::getDocumentRoot() . '/bitrix/admin',
            true,
            true
        );
        CopyDirFiles(
            __DIR__ . '/components',
            Application::getDocumentRoot() . '/bitrix/components',
            true,
            true
        );
        CopyDirFiles(
            __DIR__ . '/js',
            Application::getDocumentRoot() . '/bitrix/js',
            true,
            true
        );
        */
        return true;
    }

    function UnInstallFiles()
    {
        /*
        $DeleteAdminPath = [
            Application::getDocumentRoot() . '/bitrix/admin/multiregion_settings.php',
            Application::getDocumentRoot() . '/bitrix/admin/multiregion_vars.php',
        ];
        $DeleteComponentsPath = [
            Application::getDocumentRoot() . '/bitrix/components/IK',
        ];
        $DeleteJsPath = [
            Application::getDocumentRoot() . '/bitrix/js/ik.multiregional',
        ];

        foreach (array_merge($DeleteAdminPath, $DeleteComponentsPath, $DeleteJsPath) as $arItem) {
            Directory::deleteDirectory($arItem);
        };
        */

        return true;
    }
}