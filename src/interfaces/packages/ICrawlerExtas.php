<?php
namespace extas\interfaces\packages;

use extas\interfaces\IItem;

/**
 * Interface ICrawler
 *
 * @package extas\interfaces\packages
 * @author jeyroik@gmail.com
 */
interface ICrawlerExtas extends IItem
{
    public const FIELD__WORKING_DIRECTORY = '__wd__';
    public const FIELD__SETTINGS = '__settings__';
}
