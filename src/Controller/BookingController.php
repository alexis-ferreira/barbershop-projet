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
        $events = $bookingRepository->findAll();

        $bookings = [];

        foreach ($events as $event){

            $bookings[] = [
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

        $data = json_encode($bookings);

        return $this->render('booking/booking.html.twig', compact('data'));
    }
}
