<?php
namespace tests;

use extas\components\repositories\Repository;

class NothingRepository extends Repository
{
    protected string $name = 'nothing';
    protected string $scope = 'nothing';
    protected string $pk = 'name';
    protected string $itemClass = Nothing::class;
}
