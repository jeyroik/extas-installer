# Описание

Данный пакет позволяет устаналивать совместимые с Extas'ом сущности.

# Требования

- PHP 7.2+
- MongoDB 3+

# Установка

## Установка пакета

`composer require jeyroik/extas-installer:*`

## Подготовка к установке сущностей

Копируем дистрибутивы конфигураций:

- `cp vendor/jeyroik/extas-foundation/resources/env.dist .env` Базовый набор переменных окружения.
- `cp vendor/jeyroik/extas-foundation/resources/drivers.dist.json /path/to/configs/drivers.json`

Прежде, чем двигаться дальше, убедитесь, что актуализировали под свои нужды скопированные дистрибутивы.

## Установка сущностей

`/vednor/bin/extas i -p extas.json -r 1`

Помощь по комманде можно посмотреть следующим образом:

`/vendor/bin/extas i --help`

`i` - это короткая форма команды `install`.

## Экспорт сущностей

`/vendor/bin/extas e`

Помощь по комманде можно посмотреть следующим образом:

`/vendor/bin/extas e --help`

`e` - это короткая форма команды `export`.

# Использование

## Создание и установка пользовательской сущности

1. Создаём класс сущности.

```php
namepsace my\extas;

use extas\components\Item;

class My extends Item
{
    protected function getSubjectForExtension(): string
    {
        return 'my';
    }
}
```

2. Создаём репозиторий для сущности.

Из коробки Extas поддерживает MongoDB.

```php
namespace my\extas;

use extas\components\repositories\Repository;

class MyRepository extends Repository
{
    protected $pk = 'name';
    protected $itemClass = My::class;
    protected $scope = 'my';
    protected $name = 'names';
    protected $idAs = '';
}
```

В результате использования данного репозитория, будет создана коллекция `my__names`.

3. Придумываем имя секции для нашей сущности в конфигурации.

Пусть будет `my_names`.

4. Создаём плагин для установки нашей сущности.

```php
namespace my\extas;

use extas\components\plugins\PluginInstallDefault as InstallPlugin

class PluginInstallMyNames extends InstallPlugin
{
    protected $selfSection = 'my_names';
    protected $selfName = 'name';
    protected $selfRepositoryClass = MyRepository::class;
    protected $selfUID = 'name';
    protected $selfItemClass = My::class;
}
```

5. Добавляем плагин и интерфейс репозитория в нашу конфигурацию для extas'a.

По умолчанию, конфигурация находится в корне в файле с именем `extas.json`.
Однако, вы можете использовать любое имя - в этом случае не забудьте указать его в флаге `-p` при установке (см. ниже).

example.json
```json
{
    "name": "example",
    "plugins": [
        {"class": "my\\extas\\PluginInstallMyNames", "stage": "extas.install"}
    ],
    "my_names": [
        {"name": "Example 1"},
        {"name": "Example 2"}
    ],
    "package_classes": [
      {"interface": "my\\extas\\MyRepository", "class": "my\\extas\\MyRepository"}
    ]
}
```

6. Устанавливаем плагин.

`vendor/bin/extas i -p example.json -s 1 -r 1`

Должны увидеть что-то вроде

```
Extas installer v1.1
==========================
Class lock-file updated

Package "example" is installing...
Installing plugin "my\extas\PluginInstallMyNames"...
Plugin installed.
Finished
```

7. Устанавливаем сущность

`vendor/bin/extas i -p example.json -s 1 -r 1`

Должны увидеть что-то вроде

```
Extas installer v1.1
==========================
Class lock-file updated

Package "example" is installing...
Plugin "my\extas\PluginInstallMyNames" is already installed.
Installing name "Example 1"...
Name "Example 1" installed.
Installing name "Example 2"...
Name "Example 2" installed.
Finished
```

# Настройка

Extas поддерживает некоторые полезные переменные окружения, которые можно использоваться для желаемого размещения данных.

- `<scope>__DB` - имя БД для определённого пространства имён (см. MyRepository $scope).
- `<scope>_DB__<repo>` - имя БД для определённого репозитория (см. MyRepository $name).
- `<scope>__DSN` - DSN для определённого пространства имён.
- `<scope>_DSN__<repo>` - DSN для определённого репозитория.
- `<scope>__DRIVER` - драйвер хранилища для определённого пространства имён.
- `<scope>_DRIVER__<repo>` - драйвер хранилища для определённого репозитория.

Таким образом, при желании, можно разместить репозитории в разных бд или даже хранилищах.

Если указанные выше переменные окружения отсутствуют, то применяются соответственно для имени БД, DSN и драйвера:
- extas
- mongodb://localhost:27017
- mongo

Другими словами, по умолчанию, данные скалдываются в `MongoDB`, расположенную `локально` по порту `27017` в базу `extas`.
