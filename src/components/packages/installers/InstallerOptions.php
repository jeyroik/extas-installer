<?php
namespace extas\components\packages\installers;

use extas\interfaces\IHasClass;
use extas\interfaces\packages\installers\IInstallerOption;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Class InstallerOptions
 *
 * @package extas\components\packages\installers
 * @author jeyroik@gmail.com
 */
class InstallerOptions
{
    public const STAGE__PACKAGE = 'package';
    public const STAGE__ITEM = 'item';

    /**
     * @param string $stage
     * @param null|InputInterface $input
     *
     * @return \Generator|IHasClass
     * @throws \Exception
     */
    public static function byStage(string $stage, ?InputInterface $input)
    {
        /**
         * @var $options IInstallerOption[]
         */
        $repo = new InstallerOptionRepository();
        $options = $repo->all([
            IInstallerOption::FIELD__NAME => array_column($input->getOptions(), 'name'),
            IInstallerOption::FIELD__STAGE => $stage
        ]);

        foreach ($options as $option) {
            yield $option;
        }
    }
}
