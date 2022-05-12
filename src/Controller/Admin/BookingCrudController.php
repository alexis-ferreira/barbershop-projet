<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BookingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Type de rendez-vous'),
            DateTimeField::new('start', 'Date de début'),
            DateTimeField::new('end', 'Date de fin'),
            ColorField::new('backgroundColor', 'Couleur de fond'),
            ColorField::new('borderColor', 'Couleur de la bordure'),
            ColorField::new('textColor', 'Couleur du texte'),
        ];
    }
}
