<?php

/**
 * Article repository.
 *
 * (C)
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ArticleRepository.
 *
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Get paginated and sorted articles.
     *
     * @param int $page Page number
     *
     * @return Paginator<iterable> Paginator object
     */
    public function getPaginatedArticles(int $page): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('article')
            ->orderBy('article.createdAt', 'DESC')
            ->setMaxResults(10)
            ->setFirstResult(($page - 1) * 10);

        return new Paginator($queryBuilder->getQuery());
    }

    /**
     * Find articles by tag.
     *
     * @param Tag $tag Tag entity
     *
     * @return array List of articles
     */
    public function findByTag(Tag $tag): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.tags', 't')
            ->andWhere('t.id = :tagId')
            ->setParameter('tagId', $tag->getId())
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
