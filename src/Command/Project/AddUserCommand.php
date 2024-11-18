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
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

// the name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'project:add-user',
    description: 'Creates a new user in your ADIOS project',
    hidden: false,
)]
class AddUserCommand extends Command
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
      $name = $input->getArgument('username');
      $projectRoot = $input->getArgument('path') ?? Helper::findProjectRoot(getcwd());
        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

      $helper = $this->getHelper('question');

      // Prompt for password (hidden input)
      $passwordQuestion = new Question('Please enter password for this account: ', 'abcd');
      $passwordQuestion->setHidden(true);
      $passwordQuestion->setHiddenFallback(false);
      $password = $helper->ask($input, $output, $passwordQuestion);

      global $config;
      require_once($projectRoot . "/ConfigEnv.php");
      require_once($projectRoot . "/src/ConfigApp.php");

      require_once($projectRoot . "/src/App.php");

      try {
        $app = new AdiosApp($config, TRUE);

        $mUser = new \ADIOS\Models\User($app);
        $idUserAdministrator = $mUser->eloquent->create([
          'login' => $name,
          'password' => $mUser->hashPassword($password),
          'is_active' => 1,
        ])->id;

        $mUserRole = new \ADIOS\Models\UserRole($app);
        $idRoleAdministrator = $mUserRole->eloquent->create(['name' => 'Administrator'])->id;

        $mUserHasRole = new \ADIOS\Models\UserHasRole($app);
        $mUserHasRole->eloquent->create(['id_user' => $idUserAdministrator, 'id_role' => $idRoleAdministrator])->id;
      } catch (\Exception $e) {
        echo $e->getMessage();
      }

//      if (!$filesystem->exists($filename)) {
//        $filesystem->mkdir($filename);
//        $output->writeln("<info>File '$filename' created successfully.</info>");
//      } else {
//        $output->writeln("<error>Folder '$filename' already exists.</error>");
//        return Command::FAILURE;
//      }

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
          ->addArgument('username', InputArgument::REQUIRED, 'Username of the account you wish to add ')
          ->addArgument('path', InputArgument::OPTIONAL, 'Path to the directory in which the project is')
          ->setHelp('This command allows you to create a new ADIOS project.')
        ;
    }
}
