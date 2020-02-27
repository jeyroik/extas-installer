<?php
namespace extas\components\packages\installers;

use extas\components\repositories\Repository;

/**
 * Class InstallerOptionRepository
 *
 * @package extas\components\packages\installers
 * @author jeyroik@gmail.com
 */
class InstallerOptionRepository extends Repository
{
    protected string $itemClass = InstallerOption::class;
    protected string $name = 'installer_options';
    protected string $pk = InstallerOption::FIELD__NAME;
    protected string $scope = 'extas';
    protected string $idAs = '';
}
