<?php

// src/Service/CategoryProvider.php

namespace App\Service;

use App\Repository\CategoryRepository;

class CategoryProvider {
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository) {
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategory() {
        return $this->categoryRepository->findAll();
    }
}
