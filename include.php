<?
use Bitrix\Main\Loader;

Loader::registerAutoloadClasses(
	"ik.bonussystem",
	array(
		// lib
		"Ik\\BonusSystem\\Option" => "lib/Option.php",
		"Ik\\BonusSystem\\EventHandler" => "lib/EventHandler.php",
		"Ik\\BonusSystem\\Helper" => "lib/Helper.php",
		
		// orm
		"Ik\\BonusSystem\\Orm\\BonusTable" => "lib/Orm/BonusTable.php",
	)
);