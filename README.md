![tests](https://github.com/jeyroik/extas-installer/workflows/PHP%20Composer/badge.svg?branch=master&event=push)
![codecov.io](https://codecov.io/gh/jeyroik/extas-installer/coverage.svg?branch=master)

# Описание

Данный пакет позволяет устаналивать совместимые с Extas'ом сущности.

# Требования

- PHP 7.4+
- MongoDB 3+

# Установка

## Установка пакета

`# composer require jeyroik/extas-installer:*`

## Инициализация Extas'a

`# vendor/bin/extas init`

Эта команда создаст дефолтные контейнеры классов.

## Установка сущностей

`# vednor/bin/extas install`

## Удаление сущностей

- `# vendor/bin/extas uninstall` удалить все сущности во всех пакетах
- `# vendor/bin/extas uninstall -p <package.name>` удалить все сущности из пакета `<package.name>`. Имя пакета можно найти в `extas.json` в поле `name`. Например, у текущего пакета имя `extas/installer`.
- `# vendor/bin/extas uninstall -p <package1.name>,<package2.name>` удалить все сущности из пакетов `<package1.name>`, `<package2.name>`
- `# vendor/bin/extas uninstall -e <entity.name>` удалить во всех пакетах сущность `<entity.name>`. В качестве имени сущности используется имя секции в `extas.json`.
- `# vendor/bin/extas uninstall -p <package.name> -e <entity.name>` удалить сущность `<entity.anem>` из пакета `<package.name>`

## Экспорт сущностей

`# vendor/bin/extas export`

# Использование

## Создание и установка пользовательской сущности

1. Создаём класс сущности.

```php
namespace my\extas;

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
    protected string $pk = 'name';
    protected string $itemClass = My::class;
    protected string $scope = 'my';
    protected string $name = 'names';
    protected string $idAs = '';
}
```

В результате использования данного репозитория, будет создана коллекция `my__names`.

3. Придумываем имя секции для нашей сущности в конфигурации.

Пусть будет `my_names`.

4. Создаём плагин для установки нашей сущности.

```php
namespace my\extas;

use extas\components\plugins\PluginInstallDefault;

class PluginInstallMyNames extends PluginInstallDefault
{
    protected string $selfSection = 'my_names';
    protected string $selfName = 'my name';
    protected string $selfRepositoryClass = MyRepository::class;
    protected string $selfUID = 'name';
    protected string $selfItemClass = My::class;
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

`# vendor/bin/extas i`

Должны увидеть что-то вроде

```
Extas installer v2.0
==========================
Class lock-file updated

Package "example" is installing...
Installing plugin "my\extas\PluginInstallMyNames"...
Plugin installed.

Package "example" is installing...
Plugin "my\extas\PluginInstallMyNames" is already installed.
Installing name "Example 1"...
Name "Example 1" installed.
Installing name "Example 2"...
Name "Example 2" installed.

Finished in 1s
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
