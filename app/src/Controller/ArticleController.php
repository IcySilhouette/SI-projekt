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

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Tag;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Service\ArticleService;
use App\Service\CommentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ArticleController.
 */
#[Route('/article')]
class ArticleController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator Translator
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param Request        $request        HTTP request
     * @param ArticleService $articleService Article service
     *
     * @return Response HTTP response
     */
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(Request $request, ArticleService $articleService): Response
    {
        $page = $request->query->getInt('page', 1);
        $articles = $articleService->getPaginatedArticles($page);

        $totalArticles = count($articles);
        $maxPages = (int) ceil($totalArticles / 10);

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'current_page' => $page,
            'max_pages' => max(1, $maxPages),
        ]);
    }

    /**
     * New action.
     *
     * @param Request                $request       HTTP request
     * @param EntityManagerInterface $entityManager Entity manager
     *
     * @return Response HTTP response
     */
    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($this->getUser());
            $article->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', $this->translator->trans('message.article_created_successfully'));

            return $this->redirectToRoute('app_article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Show action.
     *
     * @param Article                $article        Article entity
     * @param Request                $request        HTTP request
     * @param EntityManagerInterface $entityManager  Entity manager
     * @param CommentService         $commentService Comment service
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'app_article_show', methods: ['GET', 'POST'])]
    public function show(Article $article, Request $request, EntityManagerInterface $entityManager, CommentService $commentService): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getUser()) {
                $this->addFlash('danger', $this->translator->trans('message.to_add_comment'));

                return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
            }

            $comment->setArticle($article);
            $comment->setAuthor($this->getUser());
            $comment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', $this->translator->trans('message.comment_added_successfully'));

            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        $page = $request->query->getInt('page', 1);
        $comments = $commentService->getPaginatedComments($article, $page);

        $totalComments = count($comments);
        $maxPages = (int) ceil($totalComments / 5);

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'comment_form' => $form->createView(),
            'comments' => $comments,
            'current_page' => $page,
            'max_pages' => max(1, $maxPages),
        ]);
    }

    /**
     * Edit action.
     *
     * @param Request                $request       HTTP request
     * @param Article                $article       Article entity
     * @param EntityManagerInterface $entityManager Entity manager
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', $this->translator->trans('message.article_updated_successfully'));

            return $this->redirectToRoute('app_article_edit', ['id' => $article->getId()]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request                $request       HTTP request
     * @param Article                $article       Article entity
     * @param EntityManagerInterface $entityManager Entity manager
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'app_article_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('success', $this->translator->trans('message.article_deleted_successfully'));
        }

        return $this->redirectToRoute('app_article_index');
    }

    /**
     * Index by tag action.
     *
     * @param Tag               $tag               Tag entity
     * @param ArticleRepository $articleRepository Article repository
     *
     * @return Response HTTP response
     */
    #[Route('/tag/{id}', name: 'app_article_by_tag', methods: ['GET'])]
    public function indexByTag(Tag $tag, ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findByTag($tag);

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'tagName' => $tag->getName(),
            'current_page' => 1,
            'max_pages' => 1,
        ]);
    }

    /**
     * By category action.
     *
     * @param Category          $category          Category entity
     * @param ArticleRepository $articleRepository Article repository
     *
     * @return Response HTTP response
     */
    #[Route('/kategoria/{id}', name: 'app_article_by_category', methods: ['GET'])]
    public function byCategory(Category $category, ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findBy(['category' => $category], ['createdAt' => 'DESC']);

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'categoryName' => $category->getName(),
            'current_page' => 1,
            'max_pages' => 1,
        ]);
    }
}
