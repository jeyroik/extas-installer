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
 * @method isAllowInstallExtension(array $extension): bool
 * @method writeLn(array $messages)
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
        $extensions = $this->package[IHasExtensions::FIELD__EXTENSIONS] ?? [];

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
            return false;
        }

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
            $this->writeLn([
                '<info>INFO: Installing extension "' . $extClass . '" [ ' . $extSubject . ' ]...</info>'
            ]);
            if (isset($extension[IInitializer::FIELD__INSTALL_ON])) {
                unset($extension[IInitializer::FIELD__INSTALL_ON]);
            }
            $extensionObj = new Extension($extension);
            $this->extensionRepo->create($extensionObj);
            $this->writeLn(['<info>CREATE: Extension installed.</info>']);
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
            $this->writeLn([
                '<info>NOTICE: Extension "' . $class . '" [ ' . $subject . ' ]</info> is already installed.'
            ]);
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

            $this->writeLn([
                '<info>UPDATE: Extension "' . $extClass . '" [ ' . $extSubject . ' ]</info> methods have been updated.'
            ]);
        }
    }
}
