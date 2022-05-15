<?php

namespace App\Controller;

use App\Repository\PortfolioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('/portfolio', name: 'app_portfolio')]
    public function portfolio(PortfolioRepository $repository)
    {
        $portfolio = $repository->findAll(); // On récupère les données

        return $this->render('portfolio/portfolio.html.twig', [
            'portfolio' => $portfolio, // On envoie les données
            'title' => 'Portfolio'
        ]);
    }

    #[Route('/', name: 'app_about')]
    public function about()
    {
        return $this->render('home/home.html.twig');
    }
}
