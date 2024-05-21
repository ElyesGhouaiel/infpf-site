<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use App\Service\DataProviderService;
use App\Service\CategoryProvider;
use App\Service\FormationProvider;
use App\Repository\CategoryRepository;
use App\Repository\FormationRepository;
use App\Entity\Category;
use App\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private $dataProviderService;

    public function __construct(DataProviderService $dataProviderService)
    {
        $this->dataProviderService = $dataProviderService;
    }

    public function getGlobals(): array
    {
        // Rend les catégories et les formations disponibles dans tous les templates Twig
        return [
            'categories' => $this->dataProviderService->getCategories(),
            'formations' => $this->dataProviderService->getFormations(),
        ];
    }
}
