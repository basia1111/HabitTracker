<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Handles user registration
 */
class RegistrationController extends AbstractController
{
    /**
     * Handles registration form and user creation
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        AuthenticationUtils $authenticationUtils,
        Security $security,
        CsrfTokenManagerInterface $csrfTokenManager 
    ): Response
    {
        // In your login controller
        if ($request->isMethod('POST')) {
            // Output CSRF debug info if enabled
            
                $submittedToken = $request->request->get('_csrf_token');
                $expectedToken = $csrfTokenManager->getToken('authenticate')->getValue();
                
                // Log or display error
                error_log("Submitted CSRF: $submittedToken");
                error_log("Expected CSRF: $expectedToken");
                error_log("Session ID: " . session_id());
            
        }

        if ($this->getUser()) {
            return $this->redirectToRoute('app_main');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $userRepository->save($user, true);

            $security->login($user, 'debug.security.authenticator.form_login.main');

            return $this->redirectToRoute('app_main');
        }

        return $this->render('security/register/index.html.twig', [
            'registrationForm' => $form->createView(),
            'error' => $error,
            'lastUsername' => $lastUsername
        ]);
    }
}