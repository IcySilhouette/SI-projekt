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
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Controller managing user administration actions.
 */
#[Route('/admin/user')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
    /**
     * Lists all users.
     *
     * @param UserRepository $userRepository User repository
     *
     * @return Response Response
     */
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * Handles the user entity editing.
     *
     * @param User                   $user          User entity
     * @param Request                $request       Request
     * @param EntityManagerInterface $entityManager Entity manager
     * @param TranslatorInterface    $translator    Translator
     *
     * @return Response Response
     */
    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $isAdmin = $request->request->get('is_admin');

            if ($email) {
                $user->setEmail($email);
            }

            $roles = ['ROLE_USER'];
            if ($isAdmin) {
                $roles[] = 'ROLE_ADMIN';
            }
            $user->setRoles($roles);

            $entityManager->flush();

            $this->addFlash('success', $translator->trans('message.user_updated_successfully'));

            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Handles the user password modification by admin.
     *
     * @param User                        $user           User entity
     * @param Request                     $request        Request
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     * @param EntityManagerInterface      $entityManager  Entity manager
     * @param TranslatorInterface         $translator     Translator
     *
     * @return Response Response
     */
    #[Route('/{id}/change-password', name: 'app_admin_user_change_password', methods: ['GET', 'POST'])]
    public function changePassword(User $user, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('new_password');

            if (!empty($newPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
                $entityManager->flush();

                $this->addFlash('success', $translator->trans('message.password_changed_successfully'));

                return $this->redirectToRoute('app_admin_user_index');
            }
        }

        return $this->render('admin/user/password.html.twig', [
            'user' => $user,
        ]);
    }
}
