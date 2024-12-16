<?php
// src/Controller/API/UserControllerAPI.php
namespace App\Controller\API;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/API')]
class UserControllerAPI extends AbstractController
{
    #[Route('/users/{id?}', name: 'user_index', methods: ['GET'])]
    public function index(?int $id, EntityManagerInterface $em): Response
    {
        $userRepository = $em->getRepository(User::class);

        if ($id === null) {
            $users = $userRepository->findAll();
            $data = array_map(function($user) {
                return $this->transformUser($user);
            }, $users);
            return $this->json($data);
        } else {
            $user = $userRepository->find($id);
            if (!$user) {
                return $this->json(['message' => 'No se ha encontrado el usuario con ID ' . $id], Response::HTTP_NOT_FOUND);
            }
            return $this->json($this->transformUser($user));
        }
    }

    #[Route('/users', name: 'user_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        $resultados = [];
        $errores = [];

        foreach ($data as $index => $userData) {
            try {
                // Verificar si los campos obligatorios están presentes
                if (!isset($userData['email']) || !isset($userData['nombre']) || !isset($userData['apellido1']) || !isset($userData['apellido2'])) {
                    throw new \Exception('Faltan datos obligatorios en el índice ' . $index);
                }

                $email = $userData['email'];
                $nombre = $userData['nombre'];
                $apellido1 = $userData['apellido1'];
                $apellido2 = $userData['apellido2'];
                $telefono = $userData['telefono'] ?? null;

                // Verificar si ya existe un usuario con el mismo email
                $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($existingUser) {
                    throw new \Exception('Ya existe un usuario con el email: ' . $email);
                }

                // Crear el nuevo usuario
                $user = new User();
                $user->setEmail($email);
                $user->setNombre($nombre);
                $user->setApellido1($apellido1);
                $user->setApellido2($apellido2);
                $user->setTelefono($telefono);
                $user->setRoles(['ROLE_USER']);
                $user->setPassword('$2y$13$pPdz4u3/CZ/CDlElL8e.Kemlfz9x2RkX0qKK0otgZBjZZ6IgYneKG');
                $user->setIsActive(true);

                $em->persist($user);
                $em->flush();

                $resultados[] = ['index' => $index, 'status' => 'success', 'message' => 'Usuario creado correctamente'];
            } catch (\Exception $e) {
                $resultados[] = ['index' => $index, 'status' => 'error', 'message' => $e->getMessage()];
                $errores[] = $index;
            }
        }

        return $this->json(['status' => 'finished', 'results' => $resultados, 'errors' => $errores], Response::HTTP_CREATED);
    }

    #[Route('/users/{id}', name: 'user_update', methods: ['PUT'])]
    public function update(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $user = $em->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->json(['error' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['user']['nombre'])) {
            $user->setNombre($data['user']['nombre']);
        }
        if (isset($data['user']['apellido1'])) {
            $user->setApellido1($data['user']['apellido1']);
        }
        if (isset($data['user']['apellido2'])) {
            $user->setApellido2($data['user']['apellido2']);
        }
        if (isset($data['user']['telefono'])) {
            $user->setTelefono($data['user']['telefono']);
        }

        try {
            $em->flush();
            return $this->json($this->transformUser($user));
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error al actualizar el usuario: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/users/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, int $id): Response
    {
        $user = $em->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->json(['error' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($user);
        $em->flush();

        return $this->json(['message' => 'Usuario eliminado exitosamente'], Response::HTTP_NO_CONTENT);
    }

    // Transforma un objeto User a un array
    private function transformUser(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'nombre' => $user->getNombre(),
            'apellido1' => $user->getApellido1(),
            'apellido2' => $user->getApellido2(),
            'telefono' => $user->getTelefono(),
            'roles' => $user->getRoles(),
            'is_verified' => $user->isVerified()
        ];
    }
}