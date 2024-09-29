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
    private const DEFAULT_MAXIMUM_BONUS_DEDUCTION_PERCENTAGE = 50; // Процент от стоимости заказа, который можно погасить бонусами

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
        try {
            $Options = ModuleOption::get_option();
            $Percent = (float) (trim($Options['ACCRUED_BONUS_PERCENTAGE'])!="") ? $Options['ACCRUED_BONUS_PERCENTAGE'] : self::DEFAULT_ACCRUED_BONUS_PERCENTAGE;
            $Percent = (int) floor($Percent);

            if( !is_int($Percent) || (int)$Percent > 100 ) throw new \Bitrix\Main\SystemException("Недопустимое значение процента начисления бонусов");
            
            $AccrualBonus = ( $Price / 100 ) * $Percent;
    
            // Событие, в котором можно изменить значение
            foreach(GetModuleEvents(IK_BONUSSYSTEM_FUNCTION_MODULE_ID, 'onAfterAccrualBonus', true) as $arEvent){
                ExecuteModuleEventEx($arEvent, [$Price, &$AccrualBonus, $OrderID]);
            }
    
            return $AccrualBonus;
        } catch (\Throwable $th) {
            throw new \Bitrix\Main\SystemException( $th->getMessage() );
        }
    }

    /**
     * Расчёт суммы начисляемых бонусов из заказа
     * @param Sale\Order $Order — Заказ
     * @return float — количество бонусов для начисления
     */
    public function getAccrualBonusFromOrder(Sale\Order $Order): float
    {
        return $this->getAccrualBonus( $Order->getPrice() - $Order->getDeliveryPrice(), $Order->getId() );
    }

    /**
     * Получаем бонусы списанные на заказ
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
            
            $AccrualedBonus = array_shift($arItem['VALUE']);
            $MaximumBonusForDeducted = $this->getMaximumBonusForDeducted($Order);
            
            return ($AccrualedBonus <= $MaximumBonusForDeducted) ? $AccrualedBonus : $MaximumBonusForDeducted;
        }
    }

    /**
     * @param Sale\Order $Order — Заказ
     * @return int — Максимальное кол-во бонусов, которым можно оплатить заказ
     */
    public function getMaximumBonusForDeducted(Sale\Order $Order): int
    {
        $OrderPrice = $Order->getPrice() - $Order->getDeliveryPrice();

        $Options = ModuleOption::get_option();
        $Percent = (float) (trim($Options['MAXIMUM_BONUS_DEDUCTION_PERCENTAGE'])!="") ? $Options['MAXIMUM_BONUS_DEDUCTION_PERCENTAGE'] : self::DEFAULT_MAXIMUM_BONUS_DEDUCTION_PERCENTAGE;
        $MaximumBonusForDeducted = floor( ($OrderPrice / 100) * $Percent );

        // Событие, в котором можно изменить значение
        foreach(GetModuleEvents(IK_BONUSSYSTEM_FUNCTION_MODULE_ID, 'onGetMaximumBonusForDeducted', true) as $arEvent){
            ExecuteModuleEventEx($arEvent, [$Order, $Percent, &$MaximumBonusForDeducted]);
        }

        return $MaximumBonusForDeducted;
    }
}
?>