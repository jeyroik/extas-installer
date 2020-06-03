<?php
namespace extas\components\plugins\install;

use extas\components\plugins\Plugin;
use extas\components\THasInput;
use extas\components\THasName;
use extas\components\THasOutput;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\stages\IStageAfterInstallSection;

/**
 * Class AfterInstallSection
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
abstract class AfterInstallSection extends Plugin implements IStageAfterInstallSection
{
    use THasInput;
    use THasOutput;
    use THasName;

    /**
     * @return string
     */
    public function getSection(): string
    {
        return $this->config[static::FIELD__SECTION] ?? '';
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->config[static::FIELD__UID] ?? '';
    }

    /**
     * @return IRepository
     * @throws \Exception
     */
    public function getRepository(): IRepository
    {
        try {
            $repoName = $this->config[static::FIELD__REPOSITORY] ?? '';
            return $this->$repoName();
        } catch (\Exception $e) {
            throw new \Exception('Missed item repository ' . $repoName);
        }
    }
}
