<?
use Ik\BonusSystem\ModuleOption;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);
$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);
Loader::includeModule($module_id);

$ModuleOption = new ModuleOption();
if ( $request->isPost() ) $ModuleOption->save_option( $_POST );
$current_options = $ModuleOption->get_option();

// test, checkbox, selectbox, multiselectbox, textarea, statictext
$aTabs = array(
    array(
        "DIV" => "edit",
        "TAB"=> Loc::getMessage("IK_BONUSSYSTEM_MAIN_TAB_NAME"),
        "TITLE" => Loc::getMessage("IK_BONUSSYSTEM_MAIN_TAB_NAME"),
        "OPTIONS" => array(
            Loc::getMessage("API_SETTINGS"), // Заголовок настроек
            array( // Настройка
                "ACCRUED_BONUS_PERCENTAGE",
                Loc::getMessage("ACCRUED_BONUS_PERCENTAGE"),
                "",
                array("text")
            ),
        )
    ),
);


// формируем табы
$aTabs = $ModuleOption->fill_params( $aTabs );
$tabControl = new CAdminTabControl(
    "tabControl",
    $aTabs
);  
$tabControl->Begin();
?>

<form id="IK_BonusSystem" action="<?=$APPLICATION->GetCurPage(); ?>?mid=<?=$module_id?>&lang=<?=LANG?>" method="post">
    <?
    foreach($aTabs as $aTab){
        if($aTab["OPTIONS"]){
            $tabControl->BeginNextTab();
            __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
        }
    }
    $tabControl->Buttons();
    ?>

    <input type="submit" name="apply_" value="<?=Loc::GetMessage("FALBAR_TOTOP_OPTIONS_INPUT_APPLY"); ?>" class="adm-btn-save"/>
    <input type="submit" name="default" value="<?=Loc::GetMessage("FALBAR_TOTOP_OPTIONS_INPUT_DEFAULT"); ?>"/>
    <?=bitrix_sessid_post()?>
</form>
<?$tabControl->End();?>