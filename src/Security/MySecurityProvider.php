<?php

namespace App\Security;

use App\Entity\User;
use App\Service\GoogleCalendarService;
use Doctrine\ORM\EntityManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;

class MySecurityProvider implements OAuthAwareUserProviderInterface
{
    private $entityManager;
    private $security;
    private $calendarService;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        GoogleCalendarService $calendarService
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->calendarService = $calendarService;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): User
    {
        $googleEmail = $response->getEmail();
        $userRepository = $this->entityManager->getRepository(User::class);
        
        // Check if there's a currently logged in user
        $currentUser = $this->security->getUser();
        
        if ($currentUser instanceof User) {
            $accessToken = $response->getAccessToken();
            $refreshToken = $response->getRefreshToken();

            // Update Google credentials
            $currentUser->setGoogleEmail($googleEmail);
            $currentUser->setGoogleAccessToken($accessToken);
            $currentUser->setGoogleRefreshToken($refreshToken);
            
            $this->entityManager->persist($currentUser);
            $this->entityManager->flush();

            // Create calendar if it doesn't exist
            if (!$currentUser->getGoogleCalendarId()) {
                $result = $this->calendarService->createHabitCalendar($currentUser);
                if ($result['success']) {
                    $currentUser->setGoogleCalendarId($result['calendar_id']);
                    $this->entityManager->persist($currentUser);
                    $this->entityManager->flush();
                }
            }
            
            return $currentUser;
        }

        // Not logged in - try to find existing user
        // First check by Google email
        $user = $userRepository->findOneBy(['googleEmail' => $googleEmail]);
        
        if (!$user) {
            // Then check by regular email
            $user = $userRepository->findOneBy(['email' => $googleEmail]);
            
            if ($user) {
                // Found user by regular email, update Google credentials
                $accessToken = $response->getAccessToken();
                $refreshToken = $response->getRefreshToken();

                $user->setGoogleEmail($googleEmail);
                $user->setGoogleAccessToken($accessToken);
                $user->setGoogleRefreshToken($refreshToken);
                
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        }

        if (!$user) {
            // Create new user if none found
            $user = new User();
            $user->setEmail($googleEmail);
            $user->setGoogleEmail($googleEmail);
            $user->setRoles(['ROLE_USER']);
            $user->setGoogleAccessToken($response->getAccessToken());
            $user->setGoogleRefreshToken($response->getRefreshToken());
            
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $user;
    }
}

