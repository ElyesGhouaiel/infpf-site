<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:move-formations-to-ia',
    description: 'Déplace les formations IA (IDs 89 et 90) vers la catégorie IA',
)]
class MoveFormationsToIACommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Récupérer la catégorie IA
        $iaCategory = $this->entityManager->getRepository(Category::class)
            ->findOneBy(['name' => 'IA']);

        if (!$iaCategory) {
            $io->error('La catégorie "IA" n\'existe pas. Exécutez d\'abord la commande app:create-ia-category');
            return Command::FAILURE;
        }

        $formationIds = [89, 90];
        $movedCount = 0;

        foreach ($formationIds as $formationId) {
            $formation = $this->entityManager->getRepository(Formation::class)->find($formationId);
            
            if ($formation) {
                $oldCategory = $formation->getCategory();
                $oldCategoryName = $oldCategory ? $oldCategory->getName() : 'Aucune';
                
                $formation->setCategory($iaCategory);
                $this->entityManager->persist($formation);
                $movedCount++;
                
                $io->info(sprintf(
                    'Formation ID %d "%s" déplacée de "%s" vers "IA"',
                    $formationId,
                    $formation->getNameFormation(),
                    $oldCategoryName
                ));
            } else {
                $io->warning('Formation avec l\'ID ' . $formationId . ' non trouvée');
            }
        }

        if ($movedCount > 0) {
            $this->entityManager->flush();
            $io->success($movedCount . ' formation(s) déplacée(s) vers la catégorie "IA"');
        } else {
            $io->warning('Aucune formation déplacée');
        }

        return Command::SUCCESS;
    }
}


