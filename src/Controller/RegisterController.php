<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        $user = new User();


        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $user->getPassword();
            $confirmPassword = $form->get('confirmPassword')->getData();


            if ($plainPassword !== $confirmPassword) {
                $this->addFlash('error', 'Passwords do not match.');

                return $this->render('security/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }


            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);


            $entityManager->persist($user);
            $entityManager->flush();


            $this->addFlash('success', 'Account created successfully.');

            return $this->redirectToRoute('app_login');
        }


        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
