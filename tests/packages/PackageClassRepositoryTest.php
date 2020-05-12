<?php
namespace tests\packages;

use Dotenv\Dotenv;
use extas\components\packages\PackageClass;
use extas\components\packages\PackageClassRepository;
use extas\interfaces\packages\IPackageClass;
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
            PackageClass::FIELD__CLASS_NAME => 'test'
        ]));
        /**
         * @var IPackageClass $class
         */
        $class = $repo->one([PackageClass::FIELD__CLASS_NAME => 'test']);
        $this->assertNotEmpty($class);

        $classes = $repo->all([PackageClass::FIELD__CLASS_NAME => 'test']);
        $byClass = array_column($classes, null, PackageClass::FIELD__CLASS_NAME);
        $this->assertArrayHasKey('test', $byClass);

        $class->setInterfaceName('is ok');
        $repo->update($class);
        $class = $repo->one([PackageClass::FIELD__CLASS_NAME => 'test']);
        $this->assertEquals('is ok', $class->getInterfaceName());

        $repo->delete([IPackageClass::FIELD__CLASS_NAME => 'test']);

        $class = $repo->one([PackageClass::FIELD__CLASS_NAME => 'test']);
        $this->assertEmpty($class);
    }
}
