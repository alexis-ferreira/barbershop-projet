<?php

namespace App\Controller;

use App\Class\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/account.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    #[Route('/sendmail', name: 'app_sendmail')]
    public function sendMail()
    {
        $mail = new Mail();
        $mail->send('lxsdomicile71@gmail.com', 'Admin Barbershop', 'Test envoi', 'Ceci est le contenu du mail.');
        return $this->redirectToRoute('app_account');
    }
}
