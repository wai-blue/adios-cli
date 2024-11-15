<?php
// src/Command/CreateUserCommand.php
namespace App\Command\Project;

use AdiosApp;
use App\DependencyInjection\Helper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

// the name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'project:install',
    description: 'Install ADIOS project',
    hidden: false,
)]
class InstallCommand extends Command
{
    public function __construct(bool $requirePassword = false)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->requirePassword = $requirePassword;

        parent::__construct();
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
	    $output->write(PHP_EOL.'

╔═══╗╔═══╗╔══╗╔═══╗╔═══╗
║╔═╗║╚╗╔╗║╚╣╠╝║╔═╗║║╔═╗║
║║─║║─║║║║─║║─║║─║║║╚══╗
║╚═╝║─║║║║─║║─║║─║║╚══╗║
║╔═╗║╔╝╚╝║╔╣╠╗║╚═╝║║╚═╝║
╚╝─╚╝╚═══╝╚══╝╚═══╝╚═══╝
' . PHP_EOL . PHP_EOL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
      global $config;
      $projectRoot = $input->getArgument('path') ?? Helper::findProjectRoot(getcwd());
      // ... put here the code to create the user

      // this method must return an integer number with the "exit status code"
      // of the command. You can also use these constants to make code more readable

      require_once($projectRoot . "/ConfigEnv.php");
      require_once($projectRoot . "/src/ConfigApp.php");

      require($projectRoot . "/src/App.php");

      $app = new AdiosApp($config, TRUE);
      $app->install();

      // return this if there was no problem running the command
      // (it's equivalent to returning int(0))
      return Command::SUCCESS;

      // or return this if some error happened during the execution
      // (it's equivalent to returning int(1))
      // return Command::FAILURE;

      // or return this to indicate incorrect command usage; e.g. invalid options
      // or missing arguments (it's equivalent to returning int(2))
      // return Command::INVALID
    }

    protected function configure(): void
    {
        $this->setDescription('Installs an ADIOS project.')
          ->addArgument('path', InputArgument::OPTIONAL, 'Path to the directory in which the project is')
          ->setHelp('This command allows you to install an ADIOS project.')
        ;
    }
}
