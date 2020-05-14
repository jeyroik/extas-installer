<?php
namespace tests;

use extas\components\packages\installers\InstallerStageItems;

/**
 * Class InstallerOptionItemsTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class InstallerOptionItemsTest extends InstallerStageItems
{
    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            /**
             * "value" missed in the existed item, should be set
             */
            [
                'name' => 'test',
                'value' => 'is ok',
                'title' => 'test'
            ],
            /**
             * "value" is different in the existed item, should be reset
             */
            [
                'name' => 'test0',
                'value' => 'is ok again',
                'title' => 'test'
            ],
            /**
             * Fully equal to the existed item, should be skipped
             */
            [
                'name' => 'test1',
                'value' => 'is ok 1',
                'title' => 'test'
            ],
            /**
             * New item
             */
            [
                'name' => 'test2',
                'value' => 'is ok again',
                'title' => 'test'
            ]
        ];
    }
}
