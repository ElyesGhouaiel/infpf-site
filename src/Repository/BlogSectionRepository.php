<?php

namespace App\Repository;

use App\Entity\BlogSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlogSection>
 *
 * @method BlogSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogSection[]    findAll()
 * @method BlogSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogSectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogSection::class);
    }

    /**
     * @return BlogSection[] Returns an array of BlogSection objects ordered by position
     */
    public function findByBlogOrderedByPosition(int $blogId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.blog = :blogId')
            ->setParameter('blogId', $blogId)
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
