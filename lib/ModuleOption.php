<?php
namespace Ik\BonusSystem;

use Bitrix\Main\ModuleManager;
use Bitrix\Main\Entity;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;

/**
 * Класс для манипуляции с настройками модуля
 * 
 * @author IvanKarshev https://github.com/IKarshev
 * @category Class
 */
class ModuleOption{

    public CONST MODULE_ID = "ik.bonussystem";

	/**
	 * Возвращает настройки
     * @return array
	 */
	public static function get_option(): array
    {
        $Option = new \Bitrix\Main\Config\Option();
		return $Option::getForModule( self::MODULE_ID );
	}

	/**
	 * Сохраняет/изменяет настройки на отправленные в формате:
	 * array(
	 * 	"property_code1"=>"value1",
	 *	"property_code2"=>"value2",
	 * );
     * 
     * @return void
	 */
	public static function save_option( array $new_settings ): void
    {
        $Option = new \Bitrix\Main\Config\Option();
		$settings = array_merge(self::get_option(), $new_settings );

        foreach ($settings as $arkey => $arItem) {
            if ( !isset($new_settings[$arkey]) ){
                $Option::delete( self::MODULE_ID, array("name"=>$arkey) );
            }else{
                $Option::set( self::MODULE_ID, $arkey, is_array($arItem) ? implode(",", $arItem):$arItem);
            };
        };
	}

    /**
     * @param array $aTabs
     * @return array
     */
    public static function fill_params( array $aTabs ):array
    {
        $new_settings = $aTabs;

        foreach ($aTabs as $Tabskey => $TabItem) {
            
            foreach ($TabItem["OPTIONS"] as $optionskey => $optionsItem) {
                if ( is_array($optionsItem) ){
                
                    $option_type = $new_settings[$Tabskey]["OPTIONS"][$optionskey][3][0];
                    $option_id = $new_settings[$Tabskey]["OPTIONS"][$optionskey][0];
                
                    if ( !isset(self::get_option()[$option_id]) ){
                        switch ($option_type) {
                            case 'checkbox':
                                $default_type_value = "N";
                                break;
                            case 'text':
                                $default_type_value = "";
                                break;
                        };
                        // set value
                        $new_settings[$Tabskey]["OPTIONS"][$optionskey][2] = $default_type_value;
                    };
                };
            };
        };

        return $new_settings;
    }
}
