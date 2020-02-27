<?php
namespace extas\components\plugins;

use extas\components\SystemContainer;
use extas\interfaces\IItem;
use extas\interfaces\repositories\IRepository;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PluginExportDefault
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
class PluginExportDefault extends Plugin
{
    protected string $selfSection = '';
    protected string $selfName = '';
    protected string $selfRepositoryClass = '';

    /**
     * @param array $config
     * @param OutputInterface $output
     */
    public function __invoke(array $config, OutputInterface $output = null)
    {
        /**
         * @var $repo IRepository
         * @var $items IItem[]
         */
        $repo = SystemContainer::getItem($this->selfRepositoryClass);
        $items = $repo->all([]);
        $config[$this->selfSection] = [];
        foreach ($items as $item) {
            if (isset($item['_id'])) {
                unset($item['_id']);
            }
            $config[$this->selfSection][] = $item->__toArray();
        }

        $output && $output->writeln([$this->selfName . ' exported']);
    }
}
