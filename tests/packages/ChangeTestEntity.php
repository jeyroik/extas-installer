<?php
namespace tests\packages;

use extas\components\packages\installers\InstallerStageItems;

/**
 * Class ChangeTestEntity
 *
 * @package tests\packages
 * @author jeyroik <jeyroik@gmail.com>
 */
class ChangeTestEntity extends InstallerStageItems
{
    public function __invoke(): array
    {
        $config = $this->getInstaller()->getPackageConfig();
        $tests = $config[$this->getPlugin()->getPluginSection()];

        foreach ($tests as &$test) {
            $test['params'] = [
                'prev_params' => $test['params'],
                'new_params' => 'Empty'
            ];
        }

        return $tests;
    }
}
