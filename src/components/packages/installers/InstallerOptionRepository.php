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
    protected $itemClass = InstallerOption::class;
    protected $name = 'installer_options';
    protected $pk = InstallerOption::FIELD__NAME;
    protected $scope = 'extas';
    protected $idAs = '';
}
