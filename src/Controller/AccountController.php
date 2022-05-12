<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\EditProfileType;
use App\Repository\BookingRepository;
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
    public function index(BookingRepository $bookingRepository): Response
    {
        $userBookings = $bookingRepository->findBy(array('user' => $this->getUser()));

        return $this->render('account/account.html.twig', [
            'title' => 'Mon compte',
            'bookings' => $userBookings
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

                $this->addFlash('success','Votre mot de passe à bien été changé');

                return $this->redirectToRoute('app_account');

            } else {

                $this->addFlash('error','Votre mot de passe actuel ne correspond pas avec celui saisi');

                return $this->redirectToRoute('app_edit_password');
            }
        }

        return $this->render('account/changePassword.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier mot de passe'
        ]);
    }

    #[Route('/compte/modifier-mes-informations', name: 'app_edit_profile')]
    public function edit_profile(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success','Vos informations ont bien été mise à jour');

            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/editProfile.html.twig', [
            'title' => 'Modifier mes informations',
            'form' => $form->createView(),
        ]);
    }


}
