<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class AdminCommentController.
 */
#[IsGranted('ROLE_ADMIN')]
class AdminCommentController extends AbstractController
{
    /**
     * Delete comment.
     *
     * @param Comment                $comment       Comment entity
     * @param EntityManagerInterface $entityManager Entity manager
     * @param Request                $request       HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/admin/comment/{id}/delete', name: 'app_admin_comment_delete', methods: ['POST'])]
    public function delete(Comment $comment, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete_comment_'.$comment->getId(), $request->request->get('_token'))) {
            $articleId = $comment->getArticle()->getId();

            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Komentarz został pomyślnie usunięty przez administratora.');

            return $this->redirectToRoute('app_article_show', ['id' => $articleId]);
        }

        $this->addFlash('error', 'Nieprawidłowy token bezpieczeństwa.');

        return $this->redirectToRoute('app_article_index');
    }
}
