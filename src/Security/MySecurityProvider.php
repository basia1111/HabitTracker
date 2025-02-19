<?php
namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class MyUserProvider implements OAuthAwareUserProviderInterface
{
    private $entityManager;
    private $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): User
    {
        $googleEmail = $response->getEmail();
        $userRepository = $this->entityManager->getRepository(User::class);
        
        // Check if there's a currently logged in user
        $currentUser = $this->security->getUser();
        
        if ($currentUser instanceof User) {
            // Link Google account to current user
            $accessToken = $response->getAccessToken();
            $refreshToken = $response->getRefreshToken();

            $currentUser->setGoogleEmail($googleEmail);
            $currentUser->setGoogleAccessToken($accessToken);
            $currentUser->setGoogleRefreshToken($refreshToken);
            
            $this->entityManager->persist($currentUser);
            $this->entityManager->flush();
            
            return $currentUser;
        }

        // Find by Google email
        $user = $userRepository->findOneBy(['googleEmail' => $googleEmail]);
        
        if (!$user) {
            // Find by regular email if no Google link found
            $user = $userRepository->findOneBy(['email' => $googleEmail]);
        }

        if ($user) {
            // Update existing user's Google tokens
            $accessToken = $response->getAccessToken();
            $refreshToken = $response->getRefreshToken();

            $user->setGoogleEmail($googleEmail);
            $user->setGoogleAccessToken($accessToken);
            $user->setGoogleRefreshToken($refreshToken);
            
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $user;
        }

        // Create new user 
        $user = new User();
        $user->setEmail($googleEmail);
        $user->setGoogleEmail($googleEmail);
        $user->setRoles(['ROLE_USER']);
        $user->setGoogleAccessToken($response->getAccessToken());
        $user->setGoogleRefreshToken($response->getRefreshToken());
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}