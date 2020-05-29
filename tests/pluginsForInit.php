<?php
namespace tests;

use extas\components\plugins\init\PluginInit;
use extas\components\plugins\init\PluginInitItem;
use extas\components\plugins\PluginEmpty;
use extas\interfaces\packages\IInitializer;
use extas\interfaces\plugins\IPlugin;
use extas\interfaces\stages\IStageInitialize;
use extas\interfaces\stages\IStageInitializeItem;
use extas\interfaces\stages\IStageInitializeSection;

return [
    [
        // should be installed
        IPlugin::FIELD__CLASS => PluginEmpty::class,
        IPlugin::FIELD__STAGE => IInitializer::STAGE__INITIALIZATION,
        IPlugin::FIELD__PRIORITY => -1
    ],
    [
        // should be installed
        IPlugin::FIELD__CLASS => PluginEmpty::class,
        IPlugin::FIELD__STAGE => 'some.other',
        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INITIALIZATION,
        IPlugin::FIELD__PRIORITY => -1
    ],
    [
        // should be installed
        IPlugin::FIELD__CLASS => PluginEmpty::class,
        IPlugin::FIELD__STAGE => 'default.init',
        IPlugin::FIELD__PRIORITY => -1
    ],
    [
        // should NOT be installed
        IPlugin::FIELD__CLASS => PluginEmpty::class,
        IPlugin::FIELD__STAGE => 'install.on',
        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INSTALL,
        IPlugin::FIELD__PRIORITY => -1
    ],
    [
        // should be installed
        IPlugin::FIELD__CLASS => PluginInit::class,
        IPlugin::FIELD__STAGE => IStageInitialize::NAME,
        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INITIALIZATION,
        IPlugin::FIELD__PRIORITY => -1
    ],
    [
        // should be installed
        IPlugin::FIELD__CLASS => PluginInstallSnuffItemsOnInit::class,
        IPlugin::FIELD__STAGE => IStageInitializeSection::NAME . '.snuffs',
        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INITIALIZATION,
        IPlugin::FIELD__PRIORITY => -1
    ],
    [
        // should be installed
        IPlugin::FIELD__CLASS => PluginInitItem::class,
        IPlugin::FIELD__STAGE => IStageInitializeItem::NAME,
        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INITIALIZATION,
        IPlugin::FIELD__PRIORITY => -1
    ]
];
