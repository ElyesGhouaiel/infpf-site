<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Blog>
 *
 * @method Blog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blog[]    findAll()
 * @method Blog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

//    /**
//     * @return Blog[] Returns an array of Blog objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    /**
     * Trouve tous les articles publiés (status=published et publishedAt <= now)
     * ou programmés dont la date est passée
     */
    public function findPublishedBlogs(string $sortOrder = 'recent'): array
    {
        $qb = $this->createQueryBuilder('b')
            ->where('(b.status = :published AND b.publishedAt IS NOT NULL AND b.publishedAt <= :now)')
            ->orWhere('(b.status = :scheduled AND b.publishedAt IS NOT NULL AND b.publishedAt <= :now)')
            ->setParameter('published', Blog::STATUS_PUBLISHED)
            ->setParameter('scheduled', Blog::STATUS_SCHEDULED)
            ->setParameter('now', new \DateTimeImmutable());

        // Définir l'ordre
        if ($sortOrder === 'oldest') {
            $qb->orderBy('b.publishedAt', 'ASC');
        } else {
            $qb->orderBy('b.publishedAt', 'DESC');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve tous les articles pour l'interface admin (tous statuts)
     */
    public function findAllForAdmin(string $sortOrder = 'recent'): array
    {
        $qb = $this->createQueryBuilder('b');

        // Trier par ID pour inclure les brouillons (ordre de création)
        if ($sortOrder === 'oldest') {
            $qb->orderBy('b.id', 'ASC');
        } else {
            $qb->orderBy('b.id', 'DESC');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les articles programmés dont la date de publication est passée
     * (utile pour une tâche cron qui publierait automatiquement)
     */
    public function findScheduledToPublish(): array
    {
        // Récupérer TOUS les articles programmés et laisser le contrôleur 
        // faire la vérification avec le bon fuseau horaire
        return $this->createQueryBuilder('b')
            ->where('b.status = :scheduled')
            ->andWhere('b.publishedAt IS NOT NULL')
            ->setParameter('scheduled', Blog::STATUS_SCHEDULED)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve le prochain article programmé à publier (pour le minuteur)
     */
    public function findNextScheduledArticle(): ?Blog
    {
        return $this->createQueryBuilder('b')
            ->where('b.status = :scheduled')
            ->andWhere('b.publishedAt IS NOT NULL')
            ->setParameter('scheduled', Blog::STATUS_SCHEDULED)
            ->orderBy('b.publishedAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve les 3 articles les plus récents pour la page d'accueil
     * Triés du plus récent au plus ancien
     */
    public function findRecentForHome(int $limit = 3): array
    {
        return $this->createQueryBuilder('b')
            ->where('(b.status = :published AND b.publishedAt IS NOT NULL AND b.publishedAt <= :now)')
            ->orWhere('(b.status = :scheduled AND b.publishedAt IS NOT NULL AND b.publishedAt <= :now)')
            ->setParameter('published', Blog::STATUS_PUBLISHED)
            ->setParameter('scheduled', Blog::STATUS_SCHEDULED)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('b.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
