<?php

namespace App\Command;

use App\Entity\Blog;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:publish-scheduled-blogs',
    description: 'Publie automatiquement les articles programmés dont la date est passée'
)]
class PublishScheduledBlogsCommand extends Command
{
    public function __construct(
        private BlogRepository $blogRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Récupérer les articles programmés à publier
        $scheduledBlogs = $this->blogRepository->findScheduledToPublish();

        if (empty($scheduledBlogs)) {
            $io->success('Aucun article programmé à publier pour le moment.');
            return Command::SUCCESS;
        }

        $publishedCount = 0;
        
        foreach ($scheduledBlogs as $blog) {
            // Changer le statut à 'published'
            $blog->setStatus(Blog::STATUS_PUBLISHED);
            $this->entityManager->persist($blog);
            $publishedCount++;
            
            $io->writeln(sprintf(
                '✅ Article publié : "%s" (programmé pour %s)',
                $blog->getTitleOne(),
                $blog->getPublishedAt()->format('d/m/Y à H:i')
            ));
        }

        $this->entityManager->flush();

        $io->success(sprintf(
            '%d article(s) ont été publiés automatiquement !',
            $publishedCount
        ));

        return Command::SUCCESS;
    }
}
