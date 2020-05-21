<?php
namespace tests;

use extas\components\repositories\Repository;

class NothingRepository extends Repository implements INothingRepository
{
    protected string $name = 'nothing';
    protected string $scope = 'nothing';
    protected string $pk = 'name';
    protected string $itemClass = Nothing::class;
}
