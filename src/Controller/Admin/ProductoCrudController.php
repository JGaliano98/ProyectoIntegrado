<?php

namespace App\Controller\Admin;

use App\Entity\Producto;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProductoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Producto::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nombre', 'Nombre'),
            TextEditorField::new('descripcion', 'Descripción'),
            MoneyField::new('precio', 'Precio')
                ->setCurrency('EUR'),
            IntegerField::new('stock', 'Stock'),
            IntegerField::new('stock_min', 'Stock Mínimo'),
            IntegerField::new('stock_max', 'Stock Máximo'),
            ImageField::new('foto', 'Foto')
                ->setUploadDir('public/uploads/productos')
                ->setBasePath('images')
                ->setRequired(false),
            AssociationField::new('categoria', 'Categoría'),
        ];
    }
}
