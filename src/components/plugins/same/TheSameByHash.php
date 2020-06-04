<?php
namespace extas\components\plugins\same;

use extas\components\plugins\Plugin;
use extas\components\THasIO;
use extas\interfaces\IItem;
use extas\interfaces\stages\IStageItemSame;

/**
 * Class TheSameByHash
 *
 * @package extas\components\plugins\same
 * @author jeyroik <jeyroik@gmail.com>
 */
class TheSameByHash extends Plugin implements IStageItemSame
{
    use THasIO;

    /**
     * @param IItem $existed
     * @param array $current
     * @param bool $theSame
     * @return bool
     */
    public function __invoke(IItem $existed, array $current, bool &$theSame): bool
    {
        $existedData = $existed->__toArray();
        $hashExisted = sha1(json_encode($existedData));
        $hashCurrent = sha1(json_encode($current));

        $theSame = $hashCurrent == $hashExisted;

        return true;
    }
}
