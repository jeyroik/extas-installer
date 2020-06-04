<?php
namespace extas\interfaces;

/**
 * Interface IHasExtensions
 *
 * @package extas\interfaces
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IHasExtensions
{
    public const FIELD__EXTENSIONS = 'extensions';

    /**
     * @return $this
     */
    public function installExtensions();

    /**
     * @param array $extension
     * @return bool
     */
    public function isAllowInstallExtension(array $extension): bool;
}
