<?php
namespace extas\interfaces\packages;

use extas\interfaces\IItem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface IUnInstaller
 *
 * @package extas\interfaces\packages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IUnInstaller extends IItem
{
    public const SUBJECT = 'extas.uninstaller';

    public const FIELD__PACKAGE = 'package';
    public const FIELD__ENTITY = 'entity';
    public const FIELD__OUTPUT = 'output';

    /**
     * For the future aims
     */
    public const FIELD__INPUT = 'input';

    /**
     * @return mixed
     */
    public function uninstall();

    /**
     * @return string
     */
    public function getPackageName(): string;

    /**
     * @return string
     */
    public function getEntityName(): string;

    /**
     * @return InputInterface|null
     */
    public function getInput(): ?InputInterface;

    /**
     * @return OutputInterface|null
     */
    public function getOutput(): ?OutputInterface;
}
