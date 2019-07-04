# Описание

Данный пакет позволяет устаналивать совместимые с Extas'ом сущности.

# Установка

## Установка пакета

`composer require jeyroik/extas-installer:*`

## Подготовка к установке сущностей

Копируем дистрибутивы конфигураций:

- `cp vendor/jeyroik/extas-foundation/resources/env.dist .env` Базовый набор переменных окружения.
- `cp vendor/jeyroik/extas-foundation/resources/drivers.dist.json /path/to/configs/drivers.json`

Прежде, чем двигаться дальше, убедитесь, что актуализировали под свои нужды скопированные дистрибутивы.

## Установка сущностей

`/vednor/bin/extas i -p extas.json -s 0 -r 1`

Помощь по комманде можно посмотреть следующим образом:

`/vendor/bin/extas i --help`

`i` - это короткая форма команды `install`.

# Использование

## Создание и установка пользовательской сущности

1. Создаём класс сущности.

```php
namepsace my\extas;

class My extends Item
{
    protected function getSubjectForExtension()
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
    protected $itemClass = my\extas\My::class;
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
    protected $selfRepositoryClass = my\extas\MyRepository::class;
    protected $selfUID = 'name';
    protected $selfItemClass = my\extas\My::class;
}
```

5. Добавляем плагин в нашу конйигурацию для extas'a.

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
    ]
}
```

6. Устанавливаем плагин.

`vendor/bin/extas i -p example.json -s 0 -r 1`

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

`vendor/bin/extas i -p example.json -s 0 -r 1`

Должны увидеть что-то вроде

```
Extas installer v1.1
==========================
Class lock-file updated

Package "example" is installing...
Plugin "my\extas\PluginInstallMyNames" is already installed.
Installing name Example 1...
Name Example 1 installed.
Installing name Example 2...
Name Example 2 installed.
Finished
```