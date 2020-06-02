<?php
namespace extas\components\packages\installers;

use extas\components\Item;
use extas\components\THasDescription;
use extas\components\THasName;
use extas\interfaces\packages\installers\IInstallerOption;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class InstallerOption
 *
 * @package extas\components\packages\installers
 * @author jeyroik@gmail.com
 */
class InstallerOption extends Item implements IInstallerOption
{
    use THasName;
    use THasDescription;

    /**
     * @return array
     */
    public function __toInputOption(): array
    {
        return [
            $this->getName(),
            $this->getShortcut(),
            $this->getMode(),
            $this->getDescription(),
            $this->getDefault()
        ];
    }

    /**
     * @return string
     */
    public function getShortcut(): string
    {
        return $this->config[static::FIELD__SHORTCUT] ?? '';
    }

    /**
     * @return int
     */
    public function getMode(): int
    {
        return $this->config[static::FIELD__MODE] ?? InputOption::VALUE_OPTIONAL;
    }

    /**
     * @return string
     */
    public function getDefault(): string
    {
        return $this->config[static::FIELD__DEFAULT] ?? '';
    }

    /**
     * @param string $shortcut
     *
     * @return IInstallerOption
     */
    public function setShortcut(string $shortcut): IInstallerOption
    {
        $this->config[static::FIELD__SHORTCUT] = $shortcut;

        return $this;
    }

    /**
     * @param int $mode
     *
     * @return IInstallerOption
     */
    public function setMode(int $mode): IInstallerOption
    {
        $this->config[static::FIELD__MODE] = $mode;

        return $this;
    }

    /**
     * @param string $default
     *
     * @return IInstallerOption
     */
    public function setDefault(string $default): IInstallerOption
    {
        $this->config[static::FIELD__DEFAULT] = $default;

        return $this;
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
