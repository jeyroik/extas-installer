<?php
namespace tests;

use extas\components\plugins\init\Init;
use extas\components\plugins\init\InitItem;
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
        // duplicating, should not be installed
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
        // should NOT be installed, cause install_on = install
        IPlugin::FIELD__CLASS => PluginEmpty::class,
        IPlugin::FIELD__STAGE => 'install.on',
        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INSTALL,
        IPlugin::FIELD__PRIORITY => -1
    ],
    [
        // should be installed
        IPlugin::FIELD__CLASS => Init::class,
        IPlugin::FIELD__STAGE => IStageInitialize::NAME,
        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INITIALIZATION,
        IPlugin::FIELD__PRIORITY => -1
    ],
    [
        // should be installed
        IPlugin::FIELD__CLASS => InstallSnuffItemsOnInit::class,
        IPlugin::FIELD__STAGE => IStageInitializeSection::NAME . '.snuffs',
        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INITIALIZATION,
        IPlugin::FIELD__PRIORITY => -1
    ],
    [
        // should be installed
        IPlugin::FIELD__CLASS => InitItem::class,
        IPlugin::FIELD__STAGE => IStageInitializeItem::NAME,
        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INITIALIZATION,
        IPlugin::FIELD__PRIORITY => -1
    ]
];
