<?php
// src/Controller/API/CategoriaControllerAPI.php
namespace App\Controller\API;

use App\Entity\Categoria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/API')]
class CategoriaControllerAPI extends AbstractController
{
    #[Route('/categorias/{id?}', name: 'categoria_index', methods: ['GET'])]
    public function index(?int $id, EntityManagerInterface $em): Response
    {
        $categoriaRepository = $em->getRepository(Categoria::class);

        if ($id === null) {
            // Si no se proporciona ID, devolver todas las categorías
            $categorias = $categoriaRepository->findAll();
            $data = array_map(function($categoria) {
                return $this->transformCategoria($categoria);
            }, $categorias);
            return $this->json($data);
        } else {
            // Si se proporciona ID, devolver la categoría específica
            $categoria = $categoriaRepository->find($id);

            if (!$categoria) {
                return $this->json(['message' => 'No se ha encontrado la categoría con ID ' . $id], Response::HTTP_NOT_FOUND);
            }

            return $this->json($this->transformCategoria($categoria));
        }
    }

    #[Route('/categorias', name: 'categoria_create', methods: ['POST'])]
public function create(Request $request, EntityManagerInterface $em): Response
{
    $data = json_decode($request->getContent(), true);
    $resultados = [];
    $errores = [];

    foreach ($data as $index => $categoriaData) {
        try {
            // Verificar si el campo Nombre de la categoría está presente
            if (!isset($categoriaData['Nombre']) || empty($categoriaData['Nombre'])) {
                throw new \Exception('El nombre de la categoría es obligatorio en el índice ' . $index);
            }

            $nombreCategoria = $categoriaData['Nombre'];
            $descripcionCategoria = $categoriaData['descripcion'] ?? '';

            // Verificar si ya existe una categoría con el mismo nombre
            $existingCategoria = $em->getRepository(Categoria::class)->findOneBy(['Nombre' => $nombreCategoria]);
            if ($existingCategoria) {
                throw new \Exception('Ya existe una categoría con el nombre: ' . $nombreCategoria);
            }

            $categoria = new Categoria();
            $categoria->setNombre($nombreCategoria);
            $categoria->setDescripcion($descripcionCategoria);

            $em->persist($categoria);
            $em->flush();

            $resultados[] = ['index' => $index, 'status' => 'success', 'message' => 'Categoría creada correctamente'];
        } catch (\Exception $e) {
            // Manejo del error para esta línea específica
            $resultados[] = ['index' => $index, 'status' => 'error', 'message' => $e->getMessage()];
            $errores[] = $index; // Añadir el índice de la línea con error al array de errores
        }
    }

    return $this->json(['status' => 'finished', 'results' => $resultados, 'errors' => $errores], Response::HTTP_CREATED);
}



#[Route('/categorias/{id}', name: 'categoria_update', methods: ['PUT'])]
public function update(Request $request, EntityManagerInterface $em, int $id): Response
{
    // Buscar la categoría por ID
    $categoria = $em->getRepository(Categoria::class)->find($id);

    if (!$categoria) {
        return $this->json(['error' => 'Categoría no encontrada'], Response::HTTP_NOT_FOUND);
    }

    $data = json_decode($request->getContent(), true);

    if (isset($data['categoria']['Nombre'])) {
        $categoria->setNombre($data['categoria']['Nombre']);
    }

    if (isset($data['categoria']['descripcion'])) {
        $categoria->setDescripcion($data['categoria']['descripcion']);
    }

    try {
        // Guardar los cambios en la base de datos
        $em->flush();
        return $this->json($this->transformCategoria($categoria));
    } catch (\Exception $e) {
        return $this->json(['error' => 'Error al actualizar la categoría: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


    #[Route('/categorias/{id}', name: 'categoria_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, int $id): Response
    {
        // Buscar la categoría por ID
        $categoria = $em->getRepository(Categoria::class)->find($id);

        if (!$categoria) {
            return $this->json(['error' => 'Categoría no encontrada'], Response::HTTP_NOT_FOUND);
        }

        // Eliminar la categoría de la base de datos
        $em->remove($categoria);
        $em->flush();

        return $this->json(['message' => 'Categoría eliminada exitosamente'], Response::HTTP_NO_CONTENT);
    }

    // Transforma un objeto Categoria a un array
    private function transformCategoria(Categoria $categoria): array
    {
        return [
            'id' => $categoria->getId(),
            'nombre' => $categoria->getNombre(),
            'descripcion' => $categoria->getDescripcion()
        ];
    }
    
}
