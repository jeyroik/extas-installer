<?php
namespace extas\components\plugins;

use extas\interfaces\packages\ICrawler;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\repositories\IRepository;
use extas\components\SystemContainer;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PluginInstallDefault
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
abstract class PluginInstallDefault extends Plugin
{
    use TInstallMessages;

    protected $selfSection = '';
    protected $selfName = '';
    protected $selfRepositoryClass = '';
    protected $selfUID = '';
    protected $selfItemClass = '';

    protected $isRewriteAllowed = null;

    /**
     * @param $installer IInstaller
     * @param $output OutputInterface
     */
    public function __invoke($installer, $output)
    {
        $serviceConfig = $installer->getPackageConfig();

        /**
         * @var $repo IRepository
         */
        $repo = SystemContainer::getItem($this->selfRepositoryClass);
        $items = $serviceConfig[$this->selfSection] ?? [];

        foreach ($items as $item) {
            $uid = $this->getUidValue($item, $serviceConfig);
            if ($existed = $repo->one([$this->selfUID => $uid])) {
                $theSame = true;
                foreach ($item as $field => $value) {
                    if (isset($existed[$field]) && ($existed[$field] != $value)) {
                        $theSame = false;
                        $existed[$field] = $value;
                    }
                }
                if (!$theSame && $this->isRewriteAllow($serviceConfig)) {
                    $this->install($uid, $output, $existed->__toArray(), $repo, 'update');
                } else {
                    $this->alreadyInstalled($uid, $this->selfName, $output);
                }
            } else {
                $this->install($uid, $output, $item, $repo, 'create');
            }
        }

        $this->afterInstall($items, $repo, $output);
    }

    /**
     * @param $config
     *
     * @return bool
     */
    protected function isRewriteAllow($config): bool
    {
        if (is_null($this->isRewriteAllowed)) {
            if (isset($config[ICrawler::FIELD__SETTINGS])) {
                $settings = $config[ICrawler::FIELD__SETTINGS];
                $this->isRewriteAllowed = $settings[ICrawler::SETTING__REWRITE_ALLOW] ?? false;
            }
        }

        return $this->isRewriteAllowed;
    }

    /**
     * @param string $uid
     * @param OutputInterface $output
     * @param array $item
     * @param IRepository $repo
     * @param string $method
     */
    protected function install($uid, $output, $item, $repo, $method = 'create')
    {
        $this->installing($uid, $this->selfName, $output);
        $itemClass = $this->selfItemClass;
        $itemObj = new $itemClass($item);
        $repo->$method($itemObj);
        $this->installed($uid, $this->selfName, $output, $method);
    }

    /**
     * @param $items array
     * @param $repo IRepository
     * @param $output OutputInterface
     */
    protected function afterInstall($items, $repo, $output)
    {
        // You can do something here
    }

    /**
     * @param $item
     * @param $packageConfig
     *
     * @return string
     */
    protected function getUidValue(&$item, $packageConfig)
    {
        return $item[$this->selfUID];
    }
}
