<?php
namespace tests\packages;

use extas\components\packages\Initializer;
use extas\components\plugins\PluginEmpty;
use extas\interfaces\packages\IInitializer;
use extas\interfaces\plugins\IPlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

class InitializerTest extends TestCase
{
    public function test()
    {
        $init = new Initializer();
        $init->run(
            [
                [
                    IInitializer::FIELD__PACKAGE_NAME => 'test',
                    IInitializer::FIELD__PLUGINS => [
                        [
                            IPlugin::FIELD__CLASS => PluginEmpty::class,
                            IPlugin::FIELD__STAGE => IInitializer::STAGE__INITIALIZATION
                        ],
                        [
                            IPlugin::FIELD__CLASS => PluginEmpty::class,
                            IPlugin::FIELD__STAGE => 'some.other',
                            IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INITIALIZATION
                        ],
                        [
                            IPlugin::FIELD__CLASS => PluginEmpty::class,
                            IPlugin::FIELD__STAGE => 'default.init'
                        ],
                        [
                            IPlugin::FIELD__CLASS => PluginEmpty::class,
                            IPlugin::FIELD__STAGE => 'install.on',
                            IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
                        ]
                    ],
                    IInitializer::FIELD__EXTENSIONS => [

                    ]
                ]
            ],
            new NullOutput()
        );
    }
}
