<?php

/**
 * Article service.
 *
 * (C)
 */

declare(strict_types=1);

namespace App\Service;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class ArticleService.
 */
class ArticleService
{
    /**
     * Article repository.
     */
    private ArticleRepository $articleRepository;

    /**
     * Constructor.
     *
     * @param ArticleRepository $articleRepository Article repository
     */
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
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
        return $this->articleRepository->getPaginatedArticles($page);
    }
}
