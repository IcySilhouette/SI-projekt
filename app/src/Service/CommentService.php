<?php
/**
 * Comment service.
 *
 * (C) Copyright
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Article;
use App\Repository\CommentRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class CommentService.
 */
readonly class CommentService
{
    /**
     * Constructor.
     *
     * @param CommentRepository $commentRepository Comment repository
     */
    public function __construct(private CommentRepository $commentRepository)
    {
    }

    /**
     * Get paginated comments.
     *
     * @param Article $article Article entity
     * @param int     $page    Page number
     *
     * @return Paginator Paginated collection
     */
    public function getPaginatedComments(Article $article, int $page = 1): Paginator
    {
        return $this->commentRepository->findPaginatedByArticle($article, $page, 5);
    }
}
