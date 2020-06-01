<?php
namespace extas\components;

use extas\components\extensions\Extension;
use extas\components\extensions\ExtensionRepository;
use extas\interfaces\extensions\IExtension;
use extas\interfaces\IHasExtensions;
use extas\interfaces\packages\IInitializer;
use extas\interfaces\repositories\IRepository;

/**
 * Trait THasExtensions
 *
 * @property array $config
 * @method bool isAllowInstallExtension(array $extension)
 * @method void writeLn(array $messages)
 *
 * @package extas\components
 * @author jeyroik <jeyroik@gmail.com>
 */
trait THasExtensions
{
    protected IRepository $extensionRepo;

    /**
     * @return $this
     */
    public function installExtensions()
    {
        $this->extensionRepo = new ExtensionRepository();
        $extensions = $this->config[IHasExtensions::FIELD__EXTENSIONS] ?? [];

        $this->writeLn(['Found ' . count($extensions) . ' extension[s].']);

        foreach ($extensions as $extension) {
            $this->installExtension($extension);
        }

        return $this;
    }

    /**
     * @param array $extension
     * @return bool
     */
    protected function installExtension(array $extension): bool
    {
        if (!$this->isAllowInstallExtension($extension)) {
            $this->commentLn(['Skipp extension "' . $extension[IExtension::FIELD__CLASS] . '" due to stage mismatch']);
            return false;
        }

        $this->writeLn(['Installing extension "' . $extension[IExtension::FIELD__CLASS] . '"...']);

        $extClass = $extension[IExtension::FIELD__CLASS] ?? '';
        $extSubject = $extension[IExtension::FIELD__SUBJECT] ?? '';

        if ($this->installArrayExtensionSubject($extSubject, $extension)) {
            return true;
        }

        $existed = $this->updateExisted($extClass, $extSubject, $extension);
        $this->createExtension($extension, $existed);

        return true;
    }

    /**
     * @param array $extension
     * @param IExtension|null $existed
     */
    protected function createExtension(array $extension, ?IExtension $existed): void
    {
        $extClass = $extension[IExtension::FIELD__CLASS] ?? '';
        $extSubject = $extension[IExtension::FIELD__SUBJECT] ?? '';

        if (!$existed) {
            $this->infoLn(['INFO: Installing extension "' . $extClass . '" [ ' . $extSubject . ' ]...']);
            if (isset($extension[IInitializer::FIELD__INSTALL_ON])) {
                unset($extension[IInitializer::FIELD__INSTALL_ON]);
            }
            $extensionObj = new Extension($extension);
            $this->extensionRepo->create($extensionObj);
            $this->infoLn(['CREATE: Extension installed.']);
        }
    }

    /**
     * @param string $class
     * @param string $subject
     * @param array $extension
     * @return IExtension|null
     */
    protected function updateExisted(string $class, string $subject, array $extension): ?IExtension
    {
        if ($existed = $this->extensionRepo->one([
            IExtension::FIELD__CLASS => $class,
            IExtension::FIELD__SUBJECT => $subject
        ])) {
            $this->infoLn(['NOTICE: Extension "' . $class . '" [ ' . $subject . ' ] is already installed.']);
            $this->updateExtensionMethods($existed, $extension);

            return $existed;
        }

        return null;
    }

    /**
     * @param $subjects
     * @param array $extension
     * @return bool
     */
    protected function installArrayExtensionSubject($subjects, array $extension): bool
    {
        if (is_array($subjects)) {
            foreach ($subjects as $subject) {
                $extension[IExtension::FIELD__SUBJECT] = $subject;
                $this->installExtension($extension);
            }

            return true;
        }

        return false;
    }

    /**
     * @param IExtension $existed
     * @param array $extension
     */
    protected function updateExtensionMethods(IExtension $existed, array $extension): void
    {
        $newMethods = array_diff($extension[IExtension::FIELD__METHODS], $existed->getMethods());

        if (!empty($newMethods)) {
            $existed->setMethods(array_merge($existed->getMethods(), $newMethods));
            $this->extensionRepo->update($existed);

            $extClass = $extension[IExtension::FIELD__CLASS] ?? '';
            $extSubject = $extension[IExtension::FIELD__SUBJECT] ?? '';

            $this->infoLn(['UPDATE: Extension "' . $extClass . '" [ ' . $extSubject . ' ] methods have been updated.']);
        }
    }
}
