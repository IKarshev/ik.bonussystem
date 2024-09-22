<?
use Ik\BonusSystem\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);
$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);
Loader::includeModule($module_id);


$Main = new IK\BonusSystem\Option();
if ( $request->isPost() ){//save settings
    $Main->save_option( $_POST );
};
$current_options = $Main->get_option();

$aTabs = array(
    array(
        "DIV" => "edit",
        "TAB"=> Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_NAME"),
        "TITLE" => Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_NAME"),
        "OPTIONS" => array(
            Loc::getMessage("API_SETTINGS"), // Заголовок настроек
            array( // Настройка
                "test_api",
                Loc::getMessage("IS_TEST_MODE"),
                "",
                array("checkbox")
            ),
            array( // Настройка
                "extEntityId",
                Loc::getMessage("EXTENTITYID"),
                "",
                array("text")
            ),
            Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_APPEARANCE"),
        )
    ),
);


// формируем табы
$aTabs = $Main->fill_params( $aTabs );
$tabControl = new CAdminTabControl(
    "tabControl",
    $aTabs
);  
$tabControl->Begin();
?>

<form id="IK_BonusSystem" action="<?=($APPLICATION->GetCurPage()); ?>?mid=<?=$module_id?>&lang=<?=LANG?>" method="post">

<?
foreach($aTabs as $aTab){

    if($aTab["OPTIONS"]){

        $tabControl->BeginNextTab();

        __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
    }
}

$tabControl->Buttons();
?>

<input type="submit" name="apply_" value="<? echo(Loc::GetMessage("FALBAR_TOTOP_OPTIONS_INPUT_APPLY")); ?>" class="adm-btn-save" />
<input type="submit" name="default" value="<? echo(Loc::GetMessage("FALBAR_TOTOP_OPTIONS_INPUT_DEFAULT")); ?>" />

<?
echo(bitrix_sessid_post());
?>
</form>
<?$tabControl->End();?>