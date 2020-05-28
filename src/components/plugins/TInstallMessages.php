<?php
namespace extas\components\plugins;

/**
 * Trait TInstallMessages
 *
 * @method getOutput() OutputInterface
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
trait TInstallMessages
{
    /**
     * @param $id
     * @param $subject
     */
    protected function alreadyInstalled($id, $subject)
    {
        $this->getOutput()->writeln([ucfirst($subject) . ' <info>"' . $id . '"</info> is already installed.']);
    }

    /**
     * @param $id
     * @param $subject
     */
    protected function installing($id, $subject)
    {
        $this->getOutput()->writeln(['Installing ' . $subject . ' <info>"' . $id . '"</info>...']);
    }

    /**
     * @param $id
     * @param $subject
     * @param $method
     */
    protected function installed($id, $subject, $method = 'create')
    {
        $action = ($method == 'update') ? 'reinstalled' : 'installed';
        $this->getOutput()->writeln([
            ucfirst($subject) . ' <info>"' . $id . '"</info> ' . $action . '.'
        ]);
    }
}
