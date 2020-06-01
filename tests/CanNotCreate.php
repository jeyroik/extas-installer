<?php
namespace tests;

/**
 * Class CanNotCreate
 *
 * @package tests
 * @author jeyroik <jeyroik@gmail.com>
 */
class CanNotCreate
{
    public function __construct()
    {
        throw new \Exception('You can not create na instance');
    }
}
