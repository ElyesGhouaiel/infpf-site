<?php

namespace App\Command;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-ia-category',
    description: 'Crée la nouvelle catégorie IA en base de données',
)]
class CreateIACategoryCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Vérifier si la catégorie IA existe déjà
        $existingCategory = $this->entityManager->getRepository(Category::class)
            ->findOneBy(['name' => 'IA']);

        if ($existingCategory) {
            $io->warning('La catégorie "IA" existe déjà avec l\'ID : ' . $existingCategory->getId());
            return Command::SUCCESS;
        }

        // Créer la nouvelle catégorie IA
        $iaCategory = new Category();
        $iaCategory->setName('IA');
        $iaCategory->setDescription('Intelligence Artificielle et formations IA génératives');

        $this->entityManager->persist($iaCategory);
        $this->entityManager->flush();

        $io->success('Catégorie "IA" créée avec succès ! ID : ' . $iaCategory->getId());

        return Command::SUCCESS;
    }
}


