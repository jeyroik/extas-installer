<?php
namespace tests;

use extas\components\plugins\Plugin;

class InstallerOptionTest extends Plugin
{
    public function __invoke(): bool
    {
        return true;
    }
}
