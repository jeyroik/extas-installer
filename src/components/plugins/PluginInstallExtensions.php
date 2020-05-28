<?php
namespace extas\components\plugins;

use extas\components\extensions\Extension;
use extas\components\plugins\install\PluginInstallSection;

/**
 * Class PluginInstallExtensions
 *
 * @package extas\components\plugins
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginInstallExtensions extends PluginInstallSection
{
    protected string $selfSection = 'extensions';
    protected string $selfName = 'extension';
    protected string $selfRepositoryClass = 'extensionRepository';
    protected string $selfUID = Extension::FIELD__ID;
    protected string $selfItemClass = Extension::class;
}
