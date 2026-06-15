<?php

/**
 * Academic Project - Internet Newspaper.
 *
 * (c) Kamil
 */

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class AppFixtures.
 */
class AppFixtures extends Fixture
{
    /**
     * Password hasher.
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * Constructor.
     *
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Load data fixtures.
     *
     * @param ObjectManager $manager Object manager
     */
    public function load(ObjectManager $manager): void
    {
        $users = [];

        $admin = new User();
        $admin->setEmail('admin@admin.com');

        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'password1');
        $admin->setPassword($hashedPassword);

        if (method_exists($admin, 'setRoles')) {
            $admin->setRoles(['ROLE_ADMIN']);
        }
        if (method_exists($admin, 'setCreatedAt')) {
            $admin->setCreatedAt(new \DateTimeImmutable());
        }
        $manager->persist($admin);
        $users[] = $admin;

        for ($i = 1; $i <= 3; ++$i) {
            $user = new User();
            $user->setEmail("user$i@user.com");

            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password1');
            $user->setPassword($hashedPassword);

            if (method_exists($user, 'setRoles')) {
                $user->setRoles(['ROLE_USER']);
            }
            if (method_exists($user, 'setCreatedAt')) {
                $user->setCreatedAt(new \DateTimeImmutable());
            }
            $manager->persist($user);
            $users[] = $user;
        }

        $categories = [];
        $categoryNames = ['Kraj', 'Świat', 'Biznes', 'Technologie', 'Kultura', 'Sport'];
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $categories[] = $category;
        }

        $tags = [];
        $tagNames = ['PILNE', 'Wywiad', 'Gospodarka', 'Sztuczna Inteligencja', 'Polska', 'Gry', 'Kino', 'Raport'];
        foreach ($tagNames as $name) {
            $tag = new Tag();
            $tag->setName($name);
            $manager->persist($tag);
            $tags[] = $tag;
        }

        $articles = [];
        for ($i = 1; $i <= 12; ++$i) {
            $article = new Article();
            $article->setTitle("Przykładowy artykuł prasowy nr $i");
            $article->setContent("To jest treść artykułu numer $i.");
            $article->setAuthor($users[array_rand($users)]);
            $article->setCategory($categories[array_rand($categories)]);

            $randomTags = array_rand($tags, 2);
            foreach ($randomTags as $tagIndex) {
                $article->addTag($tags[$tagIndex]);
            }

            if (method_exists($article, 'setCreatedAt')) {
                $article->setCreatedAt(new \DateTimeImmutable());
            }

            $manager->persist($article);
            $articles[] = $article;
        }

        for ($i = 1; $i <= 15; ++$i) {
            $comment = new Comment();
            $comment->setContent("Ciekawy artykuł, Komentarz testowy nr $i");
            $comment->setArticle($articles[array_rand($articles)]);
            $comment->setAuthor($users[array_rand($users)]);

            if (method_exists($comment, 'setCreatedAt')) {
                $comment->setCreatedAt(new \DateTimeImmutable());
            }

            $manager->persist($comment);
        }

        $manager->flush();
    }
}
