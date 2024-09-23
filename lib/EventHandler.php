<?
namespace Ik\BonusSystem;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Loader;


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
    public static function OnSalePayOrderHandler(int $orderId, string $flagSale): bool
    {
        if (!Loader::includeModule('ik.bonussystem')) return;


    }
}
?>