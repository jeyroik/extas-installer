<?php
namespace extas\components\plugins\install;

use extas\components\plugins\Plugin;
use extas\components\THasInput;
use extas\components\THasOutput;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\stages\IStageAfterInstallItem;

/**
 * Class AfterInstallItem
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
abstract class AfterInstallItem extends Plugin implements IStageAfterInstallItem
{
    use THasInput;
    use THasOutput;

    /**
     * @return IRepository
     * @throws \Exception
     */
    public function getRepository(): IRepository
    {
        $repoName = $this->config[static::FIELD__REPOSITORY] ?? '';

        try {
            return $this->$repoName();
        } catch (\Exception $e) {
            throw new \Exception('Missed item repo ' . $repoName);
        }
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->config[static::FIELD__UID] ?? '';
    }

    /**
     * @return string
     */
    public function getSection(): string
    {
        return $this->config[static::FIELD__SECTION] ?? '';
    }
}
