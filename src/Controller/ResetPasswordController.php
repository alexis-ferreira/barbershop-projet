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

            if ($user){

                $reset_password = new ResetPassword();

                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTimeImmutable());

                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                $url = $this->generateUrl('app_reset_password', [
                    'token' => $reset_password->getToken()
                ]);

                $content = "Bonjour, " . $user->getFirstname() . "<br>Vous avez demandé à réinitialiser votre mot de passe.<br><br>" . "Merci de bien vouloir cliquer <a href='$url'>sur ce lien</a> afin de créer votre nouveau mot de passe.";

                $mail = new Mail();
                $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), 'Réinitialiser votre mot de passe', $content);

                $this->addFlash('success', 'Le lien permettant de réinitialiser votre mot de passe vient de vous être envoyé.');

                return $this->redirectToRoute('app_login');

            } else {

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

        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if (!$reset_password){

            return $this->redirectToRoute('app_reset_password');
        }

        $now = new \DateTimeImmutable();

        if ($now > $reset_password->getCreatedAt()->modify('+2 hour')){

            $this->addFlash('error', 'Votre demande de réinitialisation de mot de passe à expiré, veuillez la renouveler.');

            return $this->redirectToRoute('app_reset_password');
        };

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $new_password = $form->get('new_password')->getData();

            // Hash du password
            $hashedPassword = $passwordHasher->hashPassword(
                $reset_password->getUser(),
                $new_password
            );

            $reset_password->getUser()->setPassword($hashedPassword);

            $this->entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe à bien été modifié');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig', [
            'title' => 'Nouveau mot de passe',
            'form' => $form->createView()
        ]);
    }
}
