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
    description: 'this comand allows to import categories using a json file',
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
      

    }

  
}
