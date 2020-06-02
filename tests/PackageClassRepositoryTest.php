<?php
namespace tests\packages;

use extas\interfaces\packages\IPackageClass;
use extas\components\packages\PackageClass;
use extas\components\packages\PackageClassRepository;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

/**
 * Class PackageClassRepositoryTest
 *
 * @package tests\packages
 * @author jeyroik <jeyroik@gmail.com>
 */
class PackageClassRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    public function testCRUD()
    {
        $repo = new PackageClassRepository();
        $repo->create(new PackageClass([
            PackageClass::FIELD__INTERFACE_NAME => 'test',
            PackageClass::FIELD__CLASS => 'test'
        ]));
        /**
         * @var IPackageClass $class
         */
        $class = $repo->one([PackageClass::FIELD__CLASS => 'test']);
        $this->assertNotEmpty($class);

        $classes = $repo->all([PackageClass::FIELD__CLASS => 'test']);
        $byClass = array_column($classes, null, PackageClass::FIELD__CLASS);
        $this->assertArrayHasKey('test', $byClass);

        $class->setClass('is ok');
        $repo->update($class);
        $class = $repo->one([PackageClass::FIELD__CLASS => 'is ok']);
        $this->assertNotEmpty($class);

        $repo->delete([IPackageClass::FIELD__CLASS => 'is ok']);

        $class = $repo->one([PackageClass::FIELD__CLASS => 'is ok']);
        $this->assertEmpty($class);

        $this->assertEmpty($repo->update(null, ['class' => 'unknown']));
        $class = new PackageClass([
            PackageClass::FIELD__INTERFACE_NAME => 'test',
            PackageClass::FIELD__CLASS => 'test'
        ]);
        $repo->create($class);
        $this->assertEquals(1, $repo->delete([], $class));
    }
}
