<?
namespace Ik\BonusSystem;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Internals\OrderPropsTable;
use Bitrix\Sale\PersonType;
use CSaleOrderProps;
use CSaleOrderPropsGroup;
use Exception;

Class OrderPropertys
{
    private const IK_BONUS_SYSTEM = 'IK_BONUS';

    /**
     * @throws LoaderException
     */
    public function __construct()
    {
        Loader::includeModule('sale');
    }

    /**
     * Идентификаторы типа пользователя
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private function getPersonTypeId(): array
    {
        return array_column(PersonType::getList(['select' => ['ID']])->fetchAll(), 'ID');
    }

    /**
     * Создание свойства заказа
     *
     * @return void
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function createPropertiesBonus(): void
    {
        $personType = $this->getPersonTypeId();
        foreach ($personType as $personTypeId) {
            $propsGroupId = CSaleOrderPropsGroup::Add([
                'PERSON_TYPE_ID' => $personTypeId,
                'NAME' => Loc::getMessage('IK_BONYSSYSTEM_GROUP_NAME'),
                'SORT' => 100
            ]);
            $fields = [
                'PERSON_TYPE_ID' => $personTypeId,
                'NAME' => Loc::getMessage('IK_BONYSSYSTEM_PROPERTY'),
                'TYPE' => 'TEXT',
                'REQUIED' => 'N',
                'DEFAULT_VALUE' => '0',
                'SORT' => 100,
                'CODE' => static::IK_BONUS_SYSTEM,
                'USER_PROPS' => 'N',
                'IS_LOCATION' => 'N',
                'PROPS_GROUP_ID' => $propsGroupId,
                'SIZE1' => 0,
                'SIZE2' => 0,
                'DESCRIPTION' => '',
                'IS_EMAIL' => 'N',
                'IS_PROFILE_NAME' => 'N',
                'IS_PAYER' => 'N',
            ];
            CSaleOrderProps::Add($fields);
        }
    }

    /**
     * Удаление свойств заказа
     *
     * @return void
     * @throws BitrixException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     * @throws Exception
     */
    public function deletePropertiesBonus(): void
    {
        $dbProp = OrderPropsTable::getList([
            'select' => ['ID', 'PROPS_GROUP_ID'],
            'filter' => ['CODE' => static::IK_BONUS_SYSTEM],
        ])->fetchAll();
        if (empty($dbProp)) {
            return;
        }

        foreach ($dbProp as $propItem) {
            if (!OrderPropsTable::delete($propItem['ID'])->isSuccess()) {
                throw new BitrixException('Error delete props');
            }
            CSaleOrderPropsGroup::Delete($propItem['PROPS_GROUP_ID']);
        }
    }
}
?>