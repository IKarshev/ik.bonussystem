<?
namespace Ik\BonusSystem;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Loader;
use Bitrix\Sale;

use Ik\BonusSystem\BonusOperation;
use Ik\BonusSystem\Orm\BonusTable;


/**
 * @author IvanKarshev https://github.com/IKarshev
 * @category Class
 */
Class EventHandler{
    
    /**
     * Handler события смены флага оплаты заказа
     * 
     * @param int $orderId — ID заказа
     * @param string $flagSale — флаг оплаты
     */
    public static function OnSalePayOrderHandler(int $orderId, string $flagSale)
    {
        Loader::includeModule('sale');

        try {
            $Order = Sale\Order::load($orderId);
            $userID = $Order->getUserId();
            $BonusOperation = new BonusOperation($userID);
    
            $UserBonus = BonusTable::getBonus( $userID );
            $bonusesForAccrual = $BonusOperation->getAccrualedBonusForOrder( $Order );

            // TODO - Начисление бонусов исходя стоимости заказа

            if( $flagSale === 'Y' ){
                // Оплата заказа
                BonusTable::setBonus( $UserBonus-$bonusesForAccrual, $userID);
            }elseif ($flagSale === 'N') {
                // Возврат заказа
                BonusTable::setBonus( $UserBonus+$bonusesForAccrual, $userID);
            };
        } catch (\Throwable $th) {
            throw new \Bitrix\Main\SystemException($th->getMessage());
        }
    }
}
?>