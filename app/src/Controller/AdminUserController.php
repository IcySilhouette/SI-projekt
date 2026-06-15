<?php

/**
 * @license Proprietary
 */

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
 * Kontroler obsługujący panel administratora użytkowników.
 */
#[Route('/admin/user')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
    /**
     * Wyświetla listę wszystkich użytkowników.
     *
     * @param UserRepository $userRepository
     *
     * @return Response
     */
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * Obsługuje edycję danych użytkownika.
     *
     * @param User                   $user
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface    $translator
     *
     * @return Response
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
     * Obsługuje zmianę hasła użytkownika przez administratora.
     *
     * @param User                        $user
     * @param Request                     $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface      $entityManager
     * @param TranslatorInterface         $translator
     *
     * @return Response
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
