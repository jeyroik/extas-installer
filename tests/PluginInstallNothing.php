<?php
namespace tests;

use extas\components\plugins\PluginInstallDefault;

class PluginInstallNothing extends PluginInstallDefault
{
    protected string $selfSection = 'nothings';
    protected string $selfName = 'nothing';
    protected string $selfRepositoryClass = INothingRepository::class;
    protected string $selfUID = 'name';
    protected string $selfItemClass = Nothing::class;
}
