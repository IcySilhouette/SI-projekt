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

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller managing the user profile actions.
 */
class ProfileController extends AbstractController
{
    /**
     * Renders the profile index page.
     *
     * @return Response Response
     */
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig');
    }

    /**
     * Handles the user email modification form.
     *
     * @param Request                $request       Request
     * @param EntityManagerInterface $entityManager Entity manager
     *
     * @return Response Response
     */
    #[Route('/profile/edit-email', name: 'app_profile_edit_email', methods: ['GET', 'POST'])]
    public function editEmail(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $newEmail = $request->request->get('email');
            if (filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $user->setEmail($newEmail);
                $entityManager->flush();

                $this->addFlash('success', 'Adres e-mail został zmieniony!');

                return $this->redirectToRoute('app_profile');
            }
            $this->addFlash('danger', 'Podany e-mail jest nieprawidłowy.');
        }

        return $this->render('profile/edit_email.html.twig');
    }

    /**
     * Handles the user password modification form.
     *
     * @param Request                     $request        Request
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     * @param EntityManagerInterface      $entityManager  Entity manager
     *
     * @return Response Response
     */
    #[Route('/profile/change-password', name: 'app_profile_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $currentPassword = $request->request->get('current_password');
            $newPassword = $request->request->get('new_password');

            if (empty($currentPassword) || empty($newPassword)) {
                $this->addFlash('danger', 'Pola nie mogą być puste.');
            } elseif (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('danger', 'Aktualne hasło jest niepoprawne.');
            } else {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
                $entityManager->flush();

                $this->addFlash('success', 'Hasło zostało pomyślnie zmienione!');

                return $this->redirectToRoute('app_profile');
            }
        }

        return $this->render('profile/change_password.html.twig');
    }
}
