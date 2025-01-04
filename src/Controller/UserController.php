<?php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/user/edit', name: 'user_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Security $security, UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!empty($security->getUser())) {
            $user = $security->getUser();
            $oldPassword = $user->getPassword();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if (empty($form->get('password')->getData())) {
                    $user->setPassword($oldPassword);
                    $user = $form->getData();
                    $entityManager->persist($user);
                } else {
                    $user = $form->getData();
                    $newPassword = $form->get('password')->getData();
                    $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                    $user->setPassword($hashedPassword);
                    $entityManager->persist($user);
                }
                $this->addFlash('success', 'User has been updated.');
                $entityManager->flush();

                return $this->redirectToRoute('recipe_index');
            }

            return $this->render('user/edit.html.twig', [
                'user' => $user,
                'form' => $form,
            ]);
        }

        $this->addFlash('error', 'You can\'t edit your account when you are not logged in.');
        return $this->redirectToRoute('app_login');
    }
}
