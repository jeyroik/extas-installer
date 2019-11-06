<?php
namespace extas\components\plugins;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PluginExportPackageClasses
 *
 * @stage extas.export
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
class PluginExportPackageClasses extends Plugin
{
    /**
     * @param array $config
     * @param OutputInterface $output
     */
    public function __invoke(array $config, OutputInterface $output = null)
    {
        $containerPath = getenv('EXTAS__DRIVERS_STORAGE_PATH') ?? '';
        if (is_file($containerPath)) {
            $config['package_classes'] = json_decode(file_get_contents($containerPath), true);
        }
        $output && $output->writeln([
            'Packages classes exported'
        ]);
    }
}
