<?php
namespace tests;

use extas\components\packages\installers\InstallerStageItem;

class InstallerOptionTest extends InstallerStageItem
{
    public function __invoke(): bool
    {
        $item = $this->getItem();

        return $item['value'] == 'is failed';
    }
}
