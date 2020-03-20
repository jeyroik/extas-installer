<?php
namespace extas\components\plugins;

use extas\components\stages\Stage;
use extas\interfaces\stages\IStageRepository;

/**
 * Class PluginInstallStages
 *
 * @package extas\components\plugins
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginInstallStages extends PluginInstallDefault
{
    protected string $selfItemClass = Stage::class;
    protected string $selfName = 'stage';
    protected string $selfSection = 'stages';
    protected string $selfRepositoryClass = IStageRepository::class;
    protected string $selfUID = Stage::FIELD__NAME;
}
