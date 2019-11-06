<?php
namespace extas\components\packages;

use extas\components\Item;
use extas\interfaces\packages\IExporter;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Exporter
 *
 * @package extas\components\packages
 * @author jeyroik@gmail.com
 */
class Exporter extends Item implements IExporter
{
    /**
     * @param string $fileName
     * @param array $entitiesNames
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function exportTo(string $fileName, array $entitiesNames = [], OutputInterface $output = null): bool
    {
        $result = file_put_contents(
            $this->getPath() . '/' . $fileName,
            $this->export($entitiesNames, true, $output)
        );

        return (bool) $result;
    }

    /**
     * @param array $entitiesNames
     * @param bool $asJson
     * @param OutputInterface $output
     *
     * @return array|string
     */
    public function export(array $entitiesNames = [], bool $asJson = false, OutputInterface $output = null)
    {
        $result = [];

        if (!empty($entitiesNames)) {
            foreach ($entitiesNames as $name) {
                foreach ($this->getPluginsByStage('extas.export.' . $name) as $plugin) {
                    $plugin($result, $output);
                }
            }
        } else {
            foreach ($this->getPluginsByStage('extas.export') as $plugin) {
                $plugin($result, $output);
            }
        }

        return $asJson ? json_encode($result) : $result;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->config[static::FIELD__PATH] ?? '';
    }

    /**
     * @param string $path
     *
     * @return IExporter
     */
    public function setPath(string $path): IExporter
    {
        $this->config[static::FIELD__PATH] = $path;

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
