# ik.multiregional

Мультирегиональность для 1С-Битрикс

## Установка:
1. Клонировать репозиторий в `/bitrix/modules/` или `/local/modules/`.
2. Подключить модуль в `/bitrix/php_interface/init.php` или `/local/php_interface/init.php`:
```bash
use \Bitrix\Main\Loader;
Loader::includeModule('ik.multiregional');
```