<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddBookingController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/prendre-un-rendez-vous', name: 'app_add_booking')]
    public function addBooking(Request $request, EntityManagerInterface $entityManager, BookingRepository $bookingRepository): Response
    {
        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $bookings = $bookingRepository->findAll();

            if ($bookings = $bookingRepository->findBy(array('start' => $booking->getStart()))){

                $this->addFlash('error', 'Un rendez-vous est déjà pris sur cette plage horaire !');

                return $this->redirectToRoute('app_add_booking');
            }

            $booking = $form->getData();

            $booking->setUser($this->getUser());
            $booking->setBackgroundColor('#a0a0a0');
            $booking->setBorderColor('#000000');
            $booking->setTextColor('#ffffff');
            $booking->setEnd($booking->getStart());

            $entityManager->persist($booking);
            $entityManager->flush();

            $this->addFlash('success', 'Rendez-vous confirmer !');

            return $this->redirectToRoute('app_add_booking');

        }


        return $this->render('add_booking/add_booking.html.twig', [
            'title' => 'Prendre rendez-vous',
            'form' => $form->createView(),
        ]);
    }
}
