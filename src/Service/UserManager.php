<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Uid\Uuid;

class UserManager
{
    private EntityRepository $repository;

    public function __construct(
        private UserPasswordHasherInterface $encoder,
        private EntityManagerInterface      $em,
        private TokenStorageInterface       $tokenStorage,
        private RequestStack                $requestStack,
        private EventDispatcherInterface    $eventDispatcher
    )
    {
        $this->repository = $em->getRepository(User::class);
    }

    public function getCurrentUser(): ?User
    {
        $token = $this->tokenStorage->getToken();

        if (empty($token)) {
            return null;
        }

        $user = $token->getUser();

        if (!($user instanceof User)) {
            return null;
        }

        return $user;
    }

    public function register(array $data)
    {
        $user = $this->createUser();
        $user->setEmail($data['email']);
        $user->setPlainPassword($data['password']);
        $this->update($user);
        $this->save($user);

        return $user;
    }

    public function createUser(): User
    {
        $uuid = Uuid::v7();

        return new User($uuid);
    }

    public function update(User $user): void
    {
        $password = $this->encoder->hashPassword($user, $user->getPlainPassword());
        $user->setPassword($password);
    }

    public function save(UserInterface $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function login(UserInterface $user, Request $request): void
    {
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);
        $this->requestStack->getSession()->set('_security_main', serialize($token));
        $event = new InteractiveLoginEvent($request, $token);
        $this->eventDispatcher->dispatch($event, SecurityEvents::INTERACTIVE_LOGIN);
    }

    public function findByEmail($email): ?UserInterface
    {
        return $this->repository->findOneBy(['email' => $email]);
    }

}
