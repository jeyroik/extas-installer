<?php
namespace extas\components\packages;

use extas\components\Item;
use extas\components\THasInput;
use extas\components\THasOutput;
use extas\components\THasPath;
use extas\interfaces\stages\IStageExport;
use extas\interfaces\packages\IExporter;

/**
 * Class Exporter
 *
 * @package extas\components\packages
 * @author jeyroik@gmail.com
 */
class Exporter extends Item implements IExporter
{
    use THasPath;
    use THasInput;
    use THasOutput;

    /**
     * @param string $fileName
     * @param array $entitiesNames
     *
     * @return bool
     */
    public function exportTo(string $fileName, array $entitiesNames = []): bool
    {
        $result = file_put_contents(
            $this->getPath() . '/' . $fileName,
            $this->export($entitiesNames, true)
        );

        return (bool) $result;
    }

    /**
     * @param array $entitiesNames
     * @param bool $asJson
     *
     * @return array|string
     */
    public function export(array $entitiesNames = [], bool $asJson = false)
    {
        $result = [];
        $this->exportByNames($entitiesNames, $result);
        $this->run($result);

        return $asJson ? json_encode($result) : $result;
    }

    /**
     * @param array $entitiesNames
     * @param array $result
     */
    protected function exportByNames(array $entitiesNames, array &$result): void
    {
        foreach ($entitiesNames as $name) {
            $this->run($result, IStageExport::NAME . '.' . $name);
        }
    }

    /**
     * @param array $result
     * @param string $stage
     */
    protected function run(array &$result, string $stage = IStageExport::NAME): void
    {
        $pluginConfig = [
            IStageExport::FIELD__INPUT => $this->getInput(),
            IStageExport::FIELD__OUTPUT => $this->getOutput()
        ];

        foreach ($this->getPluginsByStage($stage, $pluginConfig) as $plugin) {
            /**
             * @var IStageExport $plugin
             */
            $plugin($result);
        }
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
