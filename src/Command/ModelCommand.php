<?php
// src/Command/CreateUserCommand.php
namespace App\Command;

use App\DependencyInjection\Helper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

// the name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'model:create',
    description: 'Creates a new ADIOS model.',
    hidden: false,
)]
class ModelCommand extends Command
{
    protected static $defaultName = 'create';

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
      $filename = $input->getArgument('filename');
      $projectRoot = Helper::findProjectRoot(getcwd());
        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

      $filePath = $projectRoot . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . $filename . 'Model.php';
      $filesystem = new Filesystem();

      if (!$filesystem->exists($filePath)) {
        $filesystem->touch($filePath);
        $output->writeln("<info>File '$filename' created successfully.</info>");
      } else {
        $output->writeln("<comment>File '$filename' already exists.</comment>");
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
        $this->setDescription('Creates new model in an ADIOS project.')
          ->addArgument('filename', InputArgument::REQUIRED, 'The name of the file to create')
          ->setHelp('This command allows you to create a user...')
        ;
    }

    // ACHTUNG! Toto pravdepodobne chceme zakazat a iba pozriet ci sa v aktualnom folderi nachadza composer.json.

}
