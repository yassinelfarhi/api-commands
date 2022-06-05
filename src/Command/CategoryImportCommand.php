<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Category;

#[AsCommand(
    name: 'category:import',
    description: 'Add a short description for your command',
)]
class CategoryImportCommand extends Command
{

    
    public function __construct($projectDir,ManagerRegistry $entityManager) {

        $this->projectDir = $projectDir;
        $this->entityManager = $entityManager->getManager();

        parent::__construct();

    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // $io = new SymfonyStyle($input, $output);
        // $arg1 = $input->getArgument('arg1');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        // $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
         $file = $this->projectDir . "/public/categories.json";
         $decoder = new Serializer([new ObjectNormalizer()],[new JsonEncoder()]);

         $rows = $decoder->decode(file_get_contents($file),"json");
         
        
         foreach( $rows as $row ) {

              $category = new Category();
              $category->setName($row['name']);
              $this->entityManager->persist($category);
              $this->entityManager->flush();
         }
          return Command::SUCCESS;
        // dd($category);

    }

  
}
