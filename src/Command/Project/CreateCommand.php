<?php
// src/Command/CreateUserCommand.php
namespace App\Command\Project;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

// the name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'project:create',
    description: 'Creates a new ADIOS project',
    aliases: ['new'],
    hidden: false,
)]
class CreateCommand extends Command
{
    public function __construct(bool $requirePassword = false)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
//        $this->requirePassword = $requirePassword;

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
      $folder = $input->getArgument('path');
//      $projectRoot = $this->findProjectRoot();
        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable\

      $filesystem = new Filesystem();

      if (!$filesystem->exists($folder)) {
        exec('git clone git@github.com:wai-blue/adios-app.git ' . $folder);
        exec('cd ' . $folder . ' && composer install');
        $filesystem->mkdir($folder . DIRECTORY_SEPARATOR . 'log');

        $output->writeln('<info>Setting up MySQL configuration...</info>');

        $helper = $this->getHelper('question');

        // Prompt for password (hidden input)
        $dbHostQuestion = new Question('Enter DB host: [localhost]' . PHP_EOL, 'localhost');
        $dbHost = $helper->ask($input, $output, $dbHostQuestion);

        $dbUserQuestion = new Question('Enter DB user: [root]' . PHP_EOL, 'root');
        $dbUser = $helper->ask($input, $output, $dbUserQuestion);

        $dbPasswordQuestion = new Question('Enter DB password:' . PHP_EOL);
        $dbPasswordQuestion->setHidden(true);
        $dbPasswordQuestion->setHiddenFallback(false);
        $dbPassword = $helper->ask($input, $output, $dbPasswordQuestion);

        $dbNameQuestion = new Question('Enter DB name: [adios-app]' . PHP_EOL, 'adios-app');
        $dbName = $helper->ask($input, $output, $dbNameQuestion);

        $configEnv = file_get_contents($folder . '/ConfigEnv.php');

        $configEnv = str_replace('$config["db_host"] = "localhost";', '$config["db_host"] = "'. $dbHost .'";', $configEnv);
        $configEnv = str_replace('$config["db_user"] = "root";', '$config["db_user"] = "'. $dbUser .'";', $configEnv);
        $configEnv = str_replace('$config["db_password"] = "";', '$config["db_password"] = "'. $dbPassword .'";', $configEnv);
        $configEnv = str_replace('$config["db_name"] = "adios-app";', '$config["db_name"] = "'. $dbName .'";', $configEnv);

        $configEnvFile = fopen($folder . '/ConfigEnv.php', 'w');
        fwrite($configEnvFile, $configEnv);
        fclose($configEnvFile);

        $output->writeln('<info>Installing project...</info>');

        $installInput = new ArrayInput([
          // the command name is passed as first argument
          'command' => 'project:install',
          'path' => getcwd() . '/' . $folder,
        ]);

        $returnCode = $this->getApplication()->doRun($installInput, $output);
        $output->writeln('<info>Adding first user...</info>');

        $addUserInput = new ArrayInput([
          // the command name is passed as first argument
          'command' => 'project:add-user',
          'username' => 'administrator',
          'path' => getcwd() . '/' . $folder,
        ]);

        $returnCode = $this->getApplication()->doRun($addUserInput, $output);
        $output->writeln('<info>ADIOS project successfully initialized!</info>' . PHP_EOL);
        $output->writeln('Login credentials:');
        $output->writeln('Username: administrator');
        $output->writeln('Password: (what you have entered)');
        $output->writeln('');
        $output->writeln('If you\'d like to add more users, try the project:add-user command.');


        $output->writeln("<info>ADIOS successfully initialized in '$folder'.</info>");
      } else {
        $output->writeln("<error>Folder '$folder' already exists.</error>");
        return Command::FAILURE;
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
