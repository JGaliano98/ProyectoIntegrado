<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Generar un token de activación
            $activationToken = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
            $user->setActivationToken($activationToken);

            $entityManager->persist($user);
            $entityManager->flush();

            // Enviar correo de activación
            $email = (new TemplatedEmail())
                ->from(new Address('jlopgal0606@g.educaand.es', 'Registro - Carniceria Charcutería Hnos Galiano Herreros'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/activation_email.html.twig')
                ->context([
                    'activationToken' => $activationToken
                ]);

            $this->emailVerifier->sendEmailConfirmation($email);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/activate/{activationToken}', name: 'app_activate_account')]
    public function activateAccount(EntityManagerInterface $entityManager, string $activationToken): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['activationToken' => $activationToken]);

        if (!$user) {
            $this->addFlash('error', 'The activation token is invalid.');
            return $this->redirectToRoute('app_register');
        }

        $user->setIsActive(true);
        $user->setVerified(true);
        $user->setActivationToken(null); // Limpiar el token de activación

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Your account has been activated.');
        return $this->redirectToRoute('app_login');
    }
}
