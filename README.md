# ik.bonussystem

Бонусная система для 1С-Битрикс

## Установка:
1. Клонировать репозиторий в `/bitrix/modules/` или `/local/modules/`.
2. Установить модуль через административную панель.


## События:
1. Перед зачислением бонусов на счёт:
```
/**
 * @param float $Price — цена заказа
 * @param float &$AccrualBonus — количество бонусов для начисления (передаётся ссылкой, можно изменить)
 * @param int $OrderID — ID заказа (если номер заказа не передан, равен 0)
 */
\Bitrix\Main\EventManager::getInstance()->addEventHandler('ik.bonussystem', 'onAfterAccrualBonus', ['Local\\MyClass', 'myFunction']);
``` 