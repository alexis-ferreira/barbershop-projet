<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\EditProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{

    private $entityManager;

    /**
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/account.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    #[Route('/compte/modifier-mot-de-passe', name: 'app_edit_password')]
    public function change_password(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $old_password = $form->get('old_password')->getData();

            if ($passwordHasher->isPasswordValid($user, $old_password)){
                $new_password = $form->get('new_password')->getData();
                $password = $passwordHasher->hashPassword(
                    $user,
                    $new_password
                );

                $user->setPassword($password);

                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        }

        return $this->render('account/changePassword.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier mon mot de passe'
        ]);
    }

    #[Route('/compte/modifier-mes-informations', name: 'app_edit_profile')]
    public function edit_profile(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);


        return $this->render('account/editProfile.html.twig', [
            'title' => 'Modifier mes informations',
            'form' => $form->createView(),
        ]);
    }


}
