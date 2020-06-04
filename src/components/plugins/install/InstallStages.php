<?php
namespace extas\components\plugins;

use extas\components\plugins\install\InstallSection;
use extas\components\stages\Stage;

/**
 * Class InstallStages
 *
 * @package extas\components\plugins
 * @author jeyroik <jeyroik@gmail.com>
 */
class InstallStages extends InstallSection
{
    protected string $selfItemClass = Stage::class;
    protected string $selfName = 'stage';
    protected string $selfSection = 'stages';
    protected string $selfRepositoryClass = 'stageRepository';
    protected string $selfUID = Stage::FIELD__NAME;
}
