<?php
namespace extas\components\plugins;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait TInstallMessages
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
trait TInstallMessages
{
    /**
     * @param $id
     * @param $subject
     * @param $output OutputInterface
     */
    protected function alreadyInstalled($id, $subject, $output)
    {
        $output->writeln([
            ucfirst($subject) . ' <info>"' . $id . '"</info> is already installed.'
        ]);
    }

    /**
     * @param $id
     * @param $subject
     * @param $output OutputInterface
     */
    protected function installing($id, $subject, $output)
    {
        $output->writeln([
            'Installing ' . $subject . ' <info>"' . $id . '"</info>...'
        ]);
    }

    /**
     * @param $id
     * @param $subject
     * @param $output OutputInterface
     */
    protected function installed($id, $subject, $output)
    {
        $output->writeln([
            ucfirst($subject) . ' <info>"' . $id . '"</info> installed.'
        ]);
    }
}
