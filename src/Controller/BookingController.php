<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/booking', name: 'app_booking')]
    public function index(): Response
    {


        return $this->render('booking/booking.html.twig', [
            'title' => 'Agenda'
        ]);
    }
}
