<?
use Bitrix\Main\Loader;

if(!defined('IK_BONUSSYSTEM_FUNCTION_MODULE_ID')) define('IK_BONUSSYSTEM_FUNCTION_MODULE_ID', 'ik.bonussystem');

Loader::registerAutoloadClasses(
	"ik.bonussystem",
	array(
		// lib
		"Ik\\BonusSystem\\ModuleOption" => "lib/ModuleOption.php",
		"Ik\\BonusSystem\\EventHandler" => "lib/EventHandler.php",
		"Ik\\BonusSystem\\Helper" => "lib/Helper.php",
		"Ik\\BonusSystem\\OrderPropertys" => "lib/OrderPropertys.php",
		"Ik\\BonusSystem\\BonusOperation" => "lib/BonusOperation.php",

		// orm
		"Ik\\BonusSystem\\Orm\\BonusTable" => "lib/Orm/BonusTable.php",
	)
);