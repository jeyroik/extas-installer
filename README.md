![tests](https://github.com/jeyroik/extas-installer/workflows/PHP%20Composer/badge.svg?branch=master&event=push)
![codecov.io](https://codecov.io/gh/jeyroik/extas-installer/coverage.svg?branch=master)
<a href="https://codeclimate.com/github/jeyroik/extas-installer/maintainability"><img src="https://api.codeclimate.com/v1/badges/fe6ec4044e95484071b5/maintainability" /></a>

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

Эта команда создаст дефолтные контейнеры классов и установит корневые плагины и расширения.

У инициализации имеются следующие стадии (для всех стадий имеются соответствующие интерфейсы):

- `extas.init`: на этой стадии можно провести дополнительные операции по инициализации пакета.
- `extas.init.section`: на этой стадии можно провести дополнительные операции по инициализации секции.
- `extas.init.item`: на этой стадии можно провести дополнительные операции по инициализации конкретного элемента.

После данной операции установлены все минимально-необходимые плагины и расширения.
Если необходимо, чтобы плагин/расширение были установлены данной командой, то необходимо в конфигурации плагина/расширения добавить:

- `install_on: initialization`
- Пример:
```json
{
  "plugins": [
    {
      "class": "my\\Plugin",
      "stage": "my.stage",
      "install_on": "initialization"
    }
  ]
}
```

## Установка сущностей

`# vednor/bin/extas install -a my_app` (короткая форма: `extas i -a my_app`)

Установка состоит из двух шагов:
- Сбор конфигуарций пакетов.
- Установка найденных пакетов.

Имеется возможность с помощью плагинов подключиться к любому моменту в этих двух шагах (для всех стадий имеются соответствующие интерфейсы), стадии указаны по порядку срабатывания:

- `extas.crawl.packages`: срабатывает после сбора конфигуарций.
- `extas.install.<application.name>`: `<application.name>` берётся из опции `-a` команды установки.
- `extas.install`: на данном этапе есть возможность подключить собственный установщик для реализации какой-то особенной логики установки пакетов. 
- `extas.install.package.<package.name>`: `<package.name>` берётся из конфигурации пакета. На данном этапе можно провести дополнительные операции по установки пакета.
- `extas.install.package`: стадия, аналогичная предыдущей.
- `extas.install.section.<section.name>`: `<section.name>` - имя секции в конфигурации. На данном этапе можно провести дополнительные операции по установке секции.
- `extas.install.section`: стадия, аналогичная предыдущей.
- `extas.install.section.<section.name>.item`: на данном этапе можно провести дополнительные операции по установке конкретного элемента сущности.
- `extas.install.item`: стадия, аналогичная предыдущей.

## Удаление сущностей

- `# vendor/bin/extas uninstall` удалить все сущности во всех пакетах
- `# vendor/bin/extas uninstall -p <package.name>` удалить все сущности из пакета `<package.name>`. Имя пакета можно найти в `extas.json` в поле `name`. Например, у текущего пакета имя `extas/installer`.
- `# vendor/bin/extas uninstall -p <package1.name>,<package2.name>` удалить все сущности из пакетов `<package1.name>`, `<package2.name>`
- `# vendor/bin/extas uninstall -e <entity.name>` удалить во всех пакетах сущность `<entity.name>`. В качестве имени сущности используется имя секции в `extas.json`.
- `# vendor/bin/extas uninstall -p <package.name> -e <entity.name>` удалить сущность `<entity.anem>` из пакета `<package.name>`

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
}
```

В результате использования данного репозитория, будет создана коллекция `my__names`.

3. Придумываем имя секции для нашей сущности в конфигурации.

Пусть будет `my_names`.

4. Создаём плагин для установки нашей сущности.

```php
namespace my\extas;

use extas\components\plugins\install\PluginInstallSectionAny;

class PluginInstallMyNames extends PluginInstallSectionAny
{
    protected string $selfSection = 'my_names';
    protected string $selfName = 'my name';
    protected string $selfRepositoryClass = 'myRepository';
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
        {"class": "my\\extas\\PluginInstallMyNames", "stage": "extas.install.section.my_names"}
    ],
    "my_names": [
        {"name": "Example 1"},
        {"name": "Example 2"}
    ],
    "package_classes": [
      {"interface": "myRepository", "class": "my\\extas\\MyRepository"}
    ]
}
```

6. Устанавливаем плагин.

`# vendor/bin/extas i`

Должны увидеть что-то вроде

```
Extas installer v3.0
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