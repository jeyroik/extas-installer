<?php
namespace tests;

use extas\components\extensions\ExtensionRepositoryGet;
use extas\interfaces\extensions\IExtension;
use extas\interfaces\extensions\IExtensionRepositoryGet;
use extas\interfaces\packages\IInitializer;

return [
    [
        IExtension::FIELD__CLASS => ExtensionRepositoryGet::class,
        IExtension::FIELD__INTERFACE => IExtensionRepositoryGet::class,
        IExtension::FIELD__SUBJECT => '*',
        IExtension::FIELD__METHODS => ['snuffRepository']
    ],
    [
        IExtension::FIELD__CLASS => 'class_0',
        IExtension::FIELD__INTERFACE => 'interface_0',
        IExtension::FIELD__SUBJECT => 's_0',
        IExtension::FIELD__METHODS => ['m1'],
        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INITIALIZATION
    ],
    [
        IExtension::FIELD__CLASS => 'class_0',
        IExtension::FIELD__INTERFACE => 'interface_0',
        IExtension::FIELD__SUBJECT => 's_0',
        IExtension::FIELD__METHODS => ['m1'],
        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
    ]
];
