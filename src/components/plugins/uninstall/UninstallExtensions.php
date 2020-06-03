<?php
namespace extas\components\plugins\uninstall;

use extas\components\extensions\Extension;
use extas\components\extensions\ExtensionRepository;

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

    /**
     * Rewrite this, cause we can not lean on theory of repo-get extension is existing - we are removing it right now.
     * @param array $item
     */
    protected function runStage(array &$item): void
    {
        $repo = new ExtensionRepository();
        $repo->delete([], $item);
        $this->infoLn(['Uninstalled item from extensions.']);
    }
}
