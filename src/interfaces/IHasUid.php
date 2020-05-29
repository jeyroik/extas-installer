<?php
namespace extas\interfaces;

/**
 * Interface IHasUid
 *
 * @package extas\interfaces
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IHasUid
{
    public const FIELD__UID = 'uid';

    /**
     * @return string
     */
    public function getUid(): string;
}
