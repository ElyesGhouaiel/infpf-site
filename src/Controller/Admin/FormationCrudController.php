<?php 
namespace App\Controller\Admin;

use App\Entity\Formation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class FormationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Formation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Formation')
            ->setEntityLabelInPlural('Formations')
            ->setSearchFields(['nameFormation', 'rncp', 'certificateur']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('Informations principales');
        yield IdField::new('id')->hideOnForm();
        yield BooleanField::new('isActive', 'Active (visible sur le site)')
            ->setHelp('Décocher pour masquer la formation du site public. Les données restent en base.');
        yield TextField::new('nameFormation', 'Nom de la formation');
        yield TextareaField::new('descriptionFormation', 'Description')->hideOnIndex();
        yield AssociationField::new('category', 'Catégorie');
        yield TextField::new('dureeFormation', 'Durée');
        yield IntegerField::new('priceFormation', 'Prix (€)');
        yield TextField::new('niveau', 'Niveau')->hideOnIndex();
        yield TextField::new('langue', 'Langue')->hideOnIndex();
        yield TextField::new('lieu', 'Lieu')->hideOnIndex();

        yield FormField::addTab('Contenu pédagogique');
        yield TextareaField::new('phraseOne', 'Objectif principal')->hideOnIndex();
        yield TextareaField::new('presentation', 'Présentation')->hideOnIndex();
        yield TextareaField::new('prerequis', 'Prérequis')->hideOnIndex();
        yield TextareaField::new('atouts', 'Atouts')->hideOnIndex();
        yield TextareaField::new('programme', 'Programme')->hideOnIndex();
        yield TextareaField::new('modalitesPedagogique', 'Modalités pédagogiques')->hideOnIndex();
        yield TextareaField::new('evaluation', 'Évaluation')->hideOnIndex();

        yield FormField::addTab('Certification RS6776');
        yield TextareaField::new('objectifsPedagogiques', 'Objectifs pédagogiques (RS6776)')
            ->setHelp('Les 3 objectifs pédagogiques obligatoires pour la certification RS6776')
            ->hideOnIndex();
        yield TextareaField::new('publicVise', 'Public visé (RS6776)')
            ->setHelp('Mention exacte du public visé pour la certification RS6776')
            ->hideOnIndex();
        yield TextareaField::new('mentionCpf', 'Mention CPF obligatoire (RS6776)')
            ->setHelp('Mention légale obligatoire concernant le financement CPF')
            ->hideOnIndex();
        yield TextareaField::new('modalitesCertification', 'Modalités de certification (RS6776)')
            ->setHelp('Modalités d\'évaluation et de certification RS6776')
            ->hideOnIndex();

        yield FormField::addTab('CPF & Certification');
        yield TextField::new('rncp', 'Code RNCP/RS');
        yield TextField::new('cpfUrl', 'URL CPF')->hideOnIndex();
        yield TextField::new('certificateur', 'Certificateur');
        yield TextField::new('bloc', 'Bloc')->hideOnIndex();
        yield TextField::new('tauxReussite', 'Taux de réussite')
            ->setHelp('Ex: "N/C", "95%", "En cours de calcul"')
            ->hideOnIndex();
    }
}