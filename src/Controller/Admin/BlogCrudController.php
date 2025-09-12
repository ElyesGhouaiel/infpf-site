<?php

namespace App\Controller\Admin;

use App\Entity\Blog;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class BlogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Blog::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title_one', 'Titre principal')
                ->setRequired(true)
                ->setHelp('Le titre principal de l\'article'),
            TextareaField::new('shortDesc', 'Description courte')
                ->setNumOfRows(3)
                ->setHelp('Description courte qui apparaît dans la liste des articles'),
            TextField::new('author', 'Auteur')
                ->setHelp('Nom de l\'auteur de l\'article'),
            DateTimeField::new('publishedAt', 'Date de publication')
                ->setHelp('Date à laquelle l\'article sera publié'),
            ImageField::new('image', 'Image')
                ->setBasePath('/uploads/images/')
                ->setUploadDir('public/uploads/images/')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setHelp('Image principale de l\'article (JPG, PNG, WebP)'),
            TextareaField::new('content_one', 'Contenu principal')
                ->setNumOfRows(10)
                ->hideOnIndex()
                ->setHelp('Le contenu principal de l\'article'),
            TextField::new('title_two', 'Titre section 2')
                ->hideOnIndex(),
            TextareaField::new('content_two', 'Contenu section 2')
                ->setNumOfRows(5)
                ->hideOnIndex(),
            TextField::new('title_tree', 'Titre section 3')
                ->hideOnIndex(),
            TextareaField::new('content_tree', 'Contenu section 3')
                ->setNumOfRows(5)
                ->hideOnIndex(),
            TextField::new('sous_title_tree', 'Sous-titre section 3')
                ->hideOnIndex(),
            TextareaField::new('sous_content_tree', 'Sous-contenu section 3')
                ->setNumOfRows(3)
                ->hideOnIndex(),
            TextField::new('title_for', 'Titre section 4')
                ->hideOnIndex(),
            TextareaField::new('content_for', 'Contenu section 4')
                ->setNumOfRows(5)
                ->hideOnIndex(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('title_one', 'Titre'))
            ->add(TextFilter::new('author', 'Auteur'))
            ->add(DateTimeFilter::new('publishedAt', 'Date de publication'))
        ;
    }
}