<?php
// src/Command/CreateUserCommand.php
namespace App\Command\Project;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

// the name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'project:create',
    description: 'Creates a new ADIOS project',
    hidden: false,
)]
class CreateCommand extends Command
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
      $filename = $input->getArgument('path');
//      $projectRoot = $this->findProjectRoot();
        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

      $filesystem = new Filesystem();

      if (!$filesystem->exists($filename)) {
        $filesystem->mkdir($filename);
        $output->writeln("<info>File '$filename' created successfully.</info>");
      } else {
        $output->writeln("<error>File '$filename' already exists.</error>");
      }

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
        $this->setDescription('Creates new ADIOS project.')
          ->addArgument('path', InputArgument::REQUIRED, 'Path to the directory in which the project should be installed')
          ->setHelp('This command allows you to create a new ADIOS project.')
        ;
    }
}
