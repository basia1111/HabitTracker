<?php
namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

/**
 * Loads or creates users from OAuth responses
 */
class MyUserProvider implements OAuthAwareUserProviderInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Loads existing user or creates new one from OAuth data
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response): User
    {
        $email = $response->getEmail();
        $userRepository = $this->entityManager->getRepository(User::class);

        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user) {
            return $user;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}