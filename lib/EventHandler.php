<?
namespace Ik\BonusSystem;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Loader;
use Bitrix\Sale;

use Ik\BonusSystem\ModuleOption;
use Ik\BonusSystem\BonusOperation;
use Ik\BonusSystem\Orm\BonusTable;

/**
 * @author IvanKarshev https://github.com/IKarshev
 * @category Class
 */
Class EventHandler{
    
    /**
     * Handler событие перед созданием заказа
     * 
     * @param Event $event
     * 
     * @return void 
     */
    public static function OnSaleOrderBeforeSavedHandler(\Bitrix\Main\Event $event): void
    {
        try {
            $ModuleOption = ModuleOption::get_option();
            $Order = $event->getParameter('ENTITY');
            if (!$Order->isNew()) return;

            $userID = $Order->getUserId();
            $BonusOperation = new BonusOperation($userID);

            $UserBonus = BonusTable::getBonus( $userID );
            $BonusesForDebiting = $BonusOperation->getAccrualedBonusForOrder( $Order );

            // Списываем бонусы
            if( $BonusesForDebiting > 0 && $UserBonus >= $BonusesForDebiting ){
                BonusTable::setBonus( $UserBonus - $BonusesForDebiting, $userID);
            } elseif( $BonusesForDebiting > 0 && $UserBonus < $BonusesForDebiting ){
                // throw new \Bitrix\Main\SystemException("Недостаточно бонусов");
                // TODO - Добавить логгер
            };


            // Начислени бонусов
            $notEarnPointsWhenTheyDebited = (isset($ModuleOption['NOT_EARN_POINTS_WHEN_THEY_DEBITED']) && $ModuleOption['NOT_EARN_POINTS_WHEN_THEY_DEBITED'] == 'Y');
            if( ($notEarnPointsWhenTheyDebited && $BonusesForDebiting == 0) || !$notEarnPointsWhenTheyDebited ){
                $AccrualBonus = $BonusOperation->getAccrualBonusFromOrder($Order);
                if( $AccrualBonus > 0 ){
                    $UserBonus = BonusTable::getBonus( $userID );
                    BonusTable::setBonus( $UserBonus + $AccrualBonus, $userID);
                }
            }

        } catch (\Throwable $th) {
            throw new \Bitrix\Main\SystemException($th->getMessage());
        }   
    }

    /**
     * Handler события отмены заказа
     * 
     * @param int $orderId — ID заказа
     * @param string $value — флаг оплаты ( Y - отменено, N - не отменено )
     * @param string $description — причина отмены
     * 
     * @return void
     */
    public static function OnSaleCancelOrderHandler(int $orderId, string $value, string $description): void
    {
        try {
            Loader::includeModule('sale');
            $Order = Sale\Order::load($orderId);
            $userID = $Order->getUserId();
            $BonusOperation = new BonusOperation($userID);
    
            $UserBonus = BonusTable::getBonus( $userID );
            $bonusesForAccrual = $BonusOperation->getAccrualedBonusForOrder( $Order );

            if( $bonusesForAccrual > 0 ){
                // Начисляем бонусы при возврате заказа
                if( $value === 'Y' ) BonusTable::setBonus( $UserBonus + $bonusesForAccrual, $userID);
            }
        } catch (\Throwable $th) {
            throw new \Bitrix\Main\SystemException($th->getMessage());
        }
    }
}
?>