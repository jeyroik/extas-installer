<?php
namespace extas\interfaces;

/**
 * Interface IHasSection
 *
 * @package extas\interfaces
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IHasSection
{
    public const FIELD__SECTION = 'section';

    /**
     * @return string
     */
    public function getSection(): string;
}
