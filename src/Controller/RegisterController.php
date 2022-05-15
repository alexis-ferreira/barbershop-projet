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

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        // On crée un nouvel utilisateur
        $user = new User();

        // Le formulaire est crée et le contenu des champs est récupéré
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        // Mettre la première lettre du nom et du prénom en majuscule
        $firstnameUppercase = ucfirst($user->getFirstname());
        $lastnameUppercase = ucfirst($user->getLastname());

        // On attribue le prénom et le nom avec la première lettre en majuscule
        $user->setFirstname($firstnameUppercase);
        $user->setLastname($lastnameUppercase);

        // Si le formulaire est soumis et valide on entre dans la condition
        if ($form->isSubmitted() && $form->isValid()) {

            // On attribue au nouvel objet user les données remplies par l'utilisateur
            $user = $form->getData();

            // On récupère le password en clair
            $plaintextPassword = $user->getPassword();

            // On hash le password avec la function UserPasswordHasherInterface de Symfony
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );

            // On attribue le password hashé a l'objet user
            $user->setPassword($hashedPassword);

            // On prépare la requète pour la création
            $this->entityManager->persist($user);

            // On envoi les informations en base de donnée
            $this->entityManager->flush();

            // Envoi le message de notification
            $this->addFlash('success', 'Inscription réussie ! Vous pouvez désormais vous connecter');

            return $this->redirectToRoute('app_login');
        }

        return $this->renderForm('register/register.html.twig', [
            'form' => $form,
            'title' => "Inscription"
        ]);
    }
}
