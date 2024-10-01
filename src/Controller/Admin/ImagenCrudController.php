<?php
// src/Controller/Admin/ImagenCrudController.php

namespace App\Controller\Admin;

use App\Entity\Imagen;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class ImagenCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Imagen::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            ImageField::new('filename')
                ->setUploadDir('public/images/carrusel')  // Directorio donde se almacenarán las imágenes
                ->setBasePath('images/carrusel')  // Ruta base para mostrar las imágenes en el panel de administración
                ->setUploadedFileNamePattern('[randomhash].[extension]')  // Patrón de nombres de archivos únicos
                ->setRequired(true),  // Hace que el campo sea obligatorio
        ];
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entity): void
    {
        if ($entity instanceof Imagen) {
            // Ruta completa del archivo en el sistema de archivos
            $filesystem = new Filesystem();
            $filePath = $this->getParameter('kernel.project_dir') . '/public/images/carrusel/' . $entity->getFilename();

            // Verifica si el archivo existe y lo elimina
            if ($filesystem->exists($filePath)) {
                try {
                    $filesystem->remove($filePath);
                } catch (IOExceptionInterface $exception) {
                    $this->addFlash('error', 'Hubo un problema eliminando el archivo ' . $exception->getPath());
                }
            }
        }

        parent::deleteEntity($entityManager, $entity);
    }
}
