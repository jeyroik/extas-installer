<?php
namespace tests;

use extas\components\extensions\ExtensionRepositoryGet;
use extas\interfaces\extensions\IExtension;
use extas\interfaces\extensions\IExtensionRepositoryGet;

return [
    [
        IExtension::FIELD__CLASS => ExtensionRepositoryGet::class,
        IExtension::FIELD__INTERFACE => IExtensionRepositoryGet::class,
        IExtension::FIELD__SUBJECT => '*',
        IExtension::FIELD__METHODS => ['snuffRepository']
    ]
];
