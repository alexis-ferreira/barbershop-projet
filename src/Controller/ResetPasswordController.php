<?php

namespace App\Controller;

use App\Class\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{

    private $entityManager;

    /**
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/mot-de-passe-oublie', name: 'app_reset_password_request')]
    public function index(Request $request): Response
    {

        // Si l'utilisateur est connecté il est redirigé vers la page home
        if ($this->getUser()) {

            return $this->redirectToRoute('app_home');
        }

        // Recherche de l'utilisateur avec l'email
        if ($request->get('email')) {

            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));

            if ($user){ // Si l'utilisateur existe

                $reset_password = new ResetPassword(); // On crée un nouvel objet ResetPassword

                $reset_password->setUser($user); // On set l'utilisateur grâce à l'email récupéré
                $reset_password->setToken(uniqid()); // On set un token que l'on crée avec la fonction uniqid()
                $reset_password->setCreatedAt(new \DateTimeImmutable()); // On set la date à laquelle il a été créé avec un DateTimeImmutable (Date et heure)

                $this->entityManager->persist($reset_password); // On prépare la requête
                $this->entityManager->flush(); // On envoi en BDD

                $url = $this->generateUrl('app_reset_password', [
                    'token' => $reset_password->getToken()
                ]); // On génère une URL avec le token en paramètre

                $content = "Bonjour, " . $user->getFirstname() . "<br>Vous avez demandé à réinitialiser votre mot de passe.<br><br>" . "Merci de bien vouloir cliquer <a href='$url'>sur ce lien</a> afin de créer votre nouveau mot de passe."; // On stock le message qui est dans le mail

                $mail = new Mail(); // On crée un nouveau mail

                $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), 'Réinitialiser votre mot de passe', $content); // On envoi le mail ("adresse email", "destinaire", "sujet" , $contenuDuMessage)

                // Notification email envoyé
                $this->addFlash('success', 'Le lien permettant de réinitialiser votre mot de passe vient de vous être envoyé.');

                return $this->redirectToRoute('app_login');

            } else { // sinon renvoi une notification d'erreur, aucun compte trouvé

                $this->addFlash('error','Aucun compte ne correspond à cette adresse email.');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('reset_password/resetPassword.html.twig', [
            'title' => 'Mot de passe oublié'
        ]);
    }

    #[Route('/modifier-mot-de-passe/{token}', name: 'app_reset_password')]
    public function reset_password(Request $request, $token, UserPasswordHasherInterface $passwordHasher)
    {
        // On recherche le token dans l'entité ResetPassword
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if (!$reset_password){ // Si le token n'existe pas

            return $this->redirectToRoute('app_reset_password_request');
        }

        $now = new \DateTimeImmutable(); // On stock la date et l'heure

        if ($now > $reset_password->getCreatedAt()->modify('+2 hour')){ // Si la date est espacé de 2 heures, la demande à expiré

            $this->addFlash('error', 'Votre demande de réinitialisation de mot de passe à expiré, veuillez la renouveler.');

            return $this->redirectToRoute('app_reset_password_request');
        }

        $form = $this->createForm(ResetPasswordType::class); // On crée un formulaire avec le ResetPasswordType
        $form->handleRequest($request); // On récupère les données

        if ($form->isSubmitted() && $form->isValid()){ // Si formulaire soumis et valide

            $new_password = $form->get('new_password')->getData(); // On récupère le nouveau mot de passe en clair

            // Hash du password
            $hashedPassword = $passwordHasher->hashPassword(
                $reset_password->getUser(),
                $new_password
            );

            $reset_password->getUser()->setPassword($hashedPassword); // on récupère l'utilisateur avec son id qui a demandé la réinitialisation et on lui set le nouveau password

            $this->entityManager->flush(); // On envoie en base de données la requète

            $this->addFlash('success', 'Votre mot de passe à bien été modifié'); // Notification success

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig', [
            'title' => 'Nouveau mot de passe',
            'form' => $form->createView()
        ]);
    }
}
