<?php
namespace extas\components\packages\installers;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Trait THasInput
 *
 * @package extas\components\packages\installers
 * @author jeyroik@gmail.com
 */
trait THasInput
{
    use THasInstaller;

    /**
     * @return null|InputInterface
     */
    public function getInput(): ?InputInterface
    {
        $installer = $this->getInstaller();

        return $installer ? $installer->getInput() : null;
    }
}
