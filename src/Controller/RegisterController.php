<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {

        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        // Mettre la première lettre du nom et du prénom en majuscule
        $firstnameUppercase = ucfirst($user->getFirstname());
        $lastnameUppercase = ucfirst($user->getLastname());
        $user->setFirstname($firstnameUppercase);
        $user->setLastname($lastnameUppercase);

        if ($form->isSubmitted() && $form->isValid()){

            $user = $form->getData();

            $plaintextPassword = $user->getPassword(); // Password en clair

            // Hash du password
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );

            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $this->renderForm('register/register.html.twig', [
            'form' => $form,
            'title' => "Inscription"
        ]);
    }
}
