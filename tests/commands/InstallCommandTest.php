<?php
namespace tests\commands;

use extas\components\options\CommandOptionRepository;
use extas\components\plugins\install\InstallCrawlers;
use extas\components\plugins\install\InstallItem;
use extas\components\plugins\install\InstallPackage;
use extas\interfaces\stages\IStageInstall;
use extas\interfaces\stages\IStageInstallItem;
use extas\interfaces\stages\IStageInstallPackage;
use extas\commands\InstallCommand;
use extas\components\console\TSnuffConsole;
use extas\components\crawlers\Crawler;
use extas\components\crawlers\CrawlerRepository;
use extas\components\extensions\ExtensionRepository;
use extas\components\packages\CrawlerExtas;
use extas\components\packages\entities\EntityRepository;
use extas\components\packages\installers\InstallerOption;
use extas\components\packages\installers\InstallerOptionRepository;
use extas\components\plugins\install\InstallApplication;
use extas\components\plugins\PluginRepository;
use extas\components\plugins\TSnuffPlugins;
use extas\components\repositories\TSnuffRepository;
use extas\interfaces\stages\IStageInstallSection;
use tests\PluginGenerateData;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class InstallCommandTest
 *
 * @package tests\commands
 * @author jeyroik <jeyroik@gmail.com>
 */
class InstallCommandTest extends TestCase
{
    use TSnuffConsole;
    use TSnuffRepository;
    use TSnuffPlugins;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->registerSnuffRepos([
            'pluginRepo' => PluginRepository::class,
            'entityRepository' => EntityRepository::class,
            'extRepo' => ExtensionRepository::class,
            'installerOptionRepository' => InstallerOptionRepository::class,
            'crawlerRepository' => CrawlerRepository::class,
            'commandOptionRepository' => CommandOptionRepository::class
        ]);
    }

    protected function tearDown(): void
    {
        $this->unregisterSnuffRepos();
    }

    public function testDispatch()
    {
        /**
         * @var BufferedOutput $output
         */
        $output = $this->getOutput(true);
        $command = $this->getCommand($output);
        $command->run(
            $this->getInput([
                'application' => 'test_install_command',
                'package_filename' => 'test.extas.json'
            ]),
            $output
        );
        $outputText = $output->fetch();
        $this->assertStringContainsString(
            'Installing application test_install_command with found packages...',
            $outputText,
            'Missed application name in the current output: ' . $outputText
        );
        $this->assertStringContainsString(
            'Found 1 packages.',
            $outputText,
            'Missed packages counting in the current output: ' . $outputText
        );
        $this->assertStringContainsString(
            'See generated data in the .extas.install',
            $outputText,
            'Missed generated data path in the current output: ' . $outputText
        );

        unlink(getcwd() . '/.extas.install');
    }

    public function testRewriteGeneratedData()
    {
        /**
         * @var BufferedOutput $output
         */
        $output = $this->getOutput(true);
        $command = $this->getCommand($output);
        $command->run(
            $this->getInput([
                'application' => 'test_install_command',
                'rewrite' => true,
                'package_filename' => 'test.extas.json'
            ]),
            $output
        );
        $outputText = $output->fetch();
        $this->assertStringContainsString(
            'See generated data in the .extas.install',
            $outputText,
            'Missed generated data path in the current output: ' . $outputText
        );

        unlink(getcwd() . '/.extas.install');
    }

    /**
     * @param OutputInterface $output
     * @return InstallCommand
     * @throws \Exception
     */
    protected function getCommand(OutputInterface $output): InstallCommand
    {
        $option = new InstallerOption();
        $option->setName('test')->setDefault(true)->setMode(InputOption::VALUE_OPTIONAL)->setShortcut('t');

        $this->createWithSnuffRepo('installerOptionRepository', $option);
        $this->createWithSnuffRepo('installerOptionRepository', new InstallerOption([
            InstallerOption::FIELD__NAME => 'package',
            InstallerOption::FIELD__DEFAULT => 'reserved.name'
        ]));
        $this->createWithSnuffRepo('crawlerRepository', new Crawler([
            Crawler::FIELD__CLASS => CrawlerExtas::class
        ]));
        $this->createSnuffPlugin(InstallApplication::class, [IStageInstall::NAME]);
        $this->createSnuffPlugin(InstallPackage::class, [IStageInstallPackage::NAME]);
        $this->createSnuffPlugin(PluginGenerateData::class, [IStageInstallPackage::NAME]);
        $this->createSnuffPlugin(InstallCrawlers::class, [IStageInstallSection::NAME . '.crawlers']);
        $this->createSnuffPlugin(InstallItem::class, [IStageInstallItem::NAME]);

        return new InstallCommand();
    }
}
