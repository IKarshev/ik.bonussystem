<?
namespace Ik\BonusSystem\Orm;

use Bitrix\Main\ModuleManager;
use Bitrix\Main\Entity;
use Bitrix\Main\Application;

/**
 * Orm-таблица для хранения бонусов и операций с ними.
 * 
 * @author IvanKarshev https://github.com/IKarshev
 * @category Class
 */
Class BonusTable extends Entity\DataManager
{
    public static function getTableName()
	{
		return 'BonusSystem';
	}
	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array('primary'=>true,'autocomplete'=>true)),
			new Entity\IntegerField('USER_ID'),
			new Entity\FloatField('BONUS'),	
		);
	}

    /**
     * @param float $Bonus — бонусы
     * @param int $UserID — ID пользователя
     * 
     * @param float $Bonus — бонусы пользователя
     */
    public static function getBonus(int $UserID = 0):float
    {
        try {
            global $USER;
            $UserID = ($UserID === 0) ? $UserID : $USER->GetID();

            $result = self::getList(['select' => ['BONUS'], 'filter' => ['USER_ID' => $UserID]])->fetchAll();
            $bonus = array_shift($result)['BONUS'];
            return ($bonus) ? $bonus : 0;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * @param float $Bonus — бонусы
     * @param int $UserID — ID пользователя 
     * 
     * @return void
     */
    public static function addBonus(float $Bonus, int $UserID = 0)
    {
        try {
            global $USER;
            $UserID = ($UserID === 0) ? $UserID : $USER->GetID();

            $CurrentBonus = self::getList([
                'select' => ['ID', 'BONUS'],
                'filter' => ['USER_ID' => $UserID],
            ])->fetchAll();

            $HasRecord = empty($CurrentBonus) ? false : true;
            if ($HasRecord) {
                $CurrentBonus = array_shift($CurrentBonus);
                self::update($CurrentBonus['ID'], ['BONUS' => $CurrentBonus['BONUS'] + $Bonus]);
            } else {
                self::add(['USER_ID' => $UserID, 'BONUS' => $Bonus]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

?>