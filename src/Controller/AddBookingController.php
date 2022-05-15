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
        // On crée un nouveau rendez-vous
        $booking = new Booking();

        // Le formulaire est crée et le contenu des champs est récupéré
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide on entre dans la condition
        if ($form->isSubmitted() && $form->isValid()){

            // On récupère tout les rendez-vous
            $bookings = $bookingRepository->findAll();

            // Si la date de début du rendez-vous existe déjà en base de données, alors on rentre dans la conditions et l'on retourne une erreur comme quoi un rendez-vous est déjà prévu sur cette plage horaire
            if ($bookings = $bookingRepository->findBy(array('start' => $booking->getStart()))){

                // Notification d'erreur
                $this->addFlash('error', 'Un rendez-vous est déjà pris sur cette plage horaire !');

                return $this->redirectToRoute('app_add_booking');
            }

            // On attribue au nouvel objet booking les données remplies par l'utilisateur
            $booking = $form->getData();

            // On set au rendez-vous les différentes informations
            $booking->setUser($this->getUser()); // Ici, on récupère l'utilisateur connecté afin de le lié au rendez-vous qu'il vient de créer
            $booking->setBackgroundColor('#a0a0a0'); // On set la couleur de l'arriere plan
            $booking->setBorderColor('#000000'); // On set la couleur de la bordure
            $booking->setTextColor('#ffffff'); // On set la couleur du texte
            $booking->setEnd($booking->getStart()); // On set la date de fin

            // On prépare la requète pour la création
            $entityManager->persist($booking);

            // On envoi les informations en base de donnée
            $entityManager->flush();

            // Envoi le message de notification
            $this->addFlash('success', 'Rendez-vous confirmer !');

            return $this->redirectToRoute('app_add_booking');

        }


        return $this->render('add_booking/add_booking.html.twig', [
            'title' => 'Prendre rendez-vous',
            'form' => $form->createView(),
        ]);
    }
}
