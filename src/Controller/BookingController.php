<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/admin/booking', name: 'app_booking')]
    public function index(BookingRepository $bookingRepository): Response
    {
        $events = $bookingRepository->findAll(); // On récupère tous les rendez-vous existants

        $bookings = []; // On crée un tableau vide

        foreach ($events as $event){ // On fait une boucle pour remplir le tableau avec tout les rendez-vous

            $bookings[] = [ // on récupère toutes les données pour les envoyées dans le tableau
                'id' => $event->getId(),
                'userId' => $event->getUser(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->add(new \DateInterval('PT1H'))->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
            ];
        }

        $data = json_encode($bookings); // On stock dans la variable le tableau encodé en json pour que FullCalendar puisse le lire

        return $this->render('booking/booking.html.twig', compact('data'));
    }
}
