<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller RGPD - Page d'information sur la protection des données
 */
class RgpdController extends AbstractController
{
    /**
     * Page d'information sur la protection des données personnelles
     */
    #[Route('/rgpd', name: 'rgpd_rights', methods: ['GET'])]
    public function rights(): Response
    {
        return $this->render('rgpd/rights.html.twig');
    }
}
