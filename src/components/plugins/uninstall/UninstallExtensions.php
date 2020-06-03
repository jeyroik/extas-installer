<?php
namespace extas\components\plugins\uninstall;

use extas\components\extensions\Extension;

/**
 * Class UninstallExtensions
 *
 * @package extas\components\plugins\uninstall
 * @author jeyroik <jeyroik@gmail.com>
 */
class UninstallExtensions extends UninstallSection
{
    protected string $selfSection = 'extensions';
    protected string $selfName = 'extension';
    protected string $selfRepositoryClass = 'extensionRepository';
    protected string $selfUID = Extension::FIELD__CLASS;
    protected string $selfItemClass = Extension::class;
}
