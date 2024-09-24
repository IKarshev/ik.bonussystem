<?
namespace Ik\BonusSystem;

use Bitrix\Main\Loader;
use Bitrix\Sale;

use Ik\BonusSystem\ModuleOption;
use Ik\BonusSystem\Orm\BonusTable;

Loader::includeModule('sale');

/**
 * Вычисления по бонусам
 * 
 * @author IvanKarshev https://github.com/IKarshev
 * @category Class
 */
Class BonusOperation
{
    public int $UserID;
    private const DEFAULT_ACCRUED_BONUS_PERCENTAGE = 15; // Процент начисления бонусов по умолчанию

    public function __construct(int $UserID) {
        $this->UserID = $UserID;
    }

    /**
     * Расчёт суммы начисляемых бонусов
     * 
     * @param float $Price — цена заказа
     * @param int $OrderID — ID заказа
     * @return float — количество бонусов для начисления
     */
    public function getAccrualBonus(float $Price, int $OrderID = 0): float
    {
        $ModuleOption = new ModuleOption();
        $Options = $ModuleOption->get_option();
        $Percent = (trim($Options['ACCRUED_BONUS_PERCENTAGE'])!="") ? (float)$Options['ACCRUED_BONUS_PERCENTAGE'] : (float) self::DEFAULT_ACCRUED_BONUS_PERCENTAGE;
        $AccrualBonus = ( $Price / 100 ) * $Percent;

        // Событие, в котором можно изменить значение
        foreach(GetModuleEvents(IK_BONUSSYSTEM_FUNCTION_MODULE_ID, 'onAfterAccrualBonus', true) as $arEvent){
            ExecuteModuleEventEx($arEvent, [$Price, &$AccrualBonus, $OrderID]);
        }

        return $AccrualBonus;
    }

    /**
     * Расчёт суммы начисляемых бонусов из заказа
     * @param Sale\Order $Order — Заказ
     * @return float — количество бонусов для начисления
     */
    public function getAccrualBonusFromOrder(Sale\Order $Order): float
    {
        return $this->getAccrualBonus( $Order->getPrice(), $Order->getId() );
    }

    /**
     * Получаем бонусы начисленные за заказ
     * @param Sale\Order $Order — Заказ
     * 
     * @return float — количество бонусов
     */
    public function getAccrualedBonusForOrder(Sale\Order $Order): float
    {
        $propertyCollection = $Order->getPropertyCollection();
        foreach ($propertyCollection->getArray()['properties'] as $arkey => $arItem) {
            if( $arItem['CODE'] != 'IK_BONUS' ) continue;
            if( !isset($arItem['VALUE']) || empty($arItem['VALUE']) ) return 0;
            return array_shift($arItem['VALUE']);
        }
    }
}
?>