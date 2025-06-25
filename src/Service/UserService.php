<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $em,
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly JWTEncoderInterface $jwtEncoder
    ) {}

    public function getAllUsersData(): array
    {
        $users = $this->userRepository->findAll();
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'adress' => $user->getAdress(),
                'postalCode' => $user->getPostalCode(),
                'city' => $user->getCity(),
            ];
        }

        return $data;
    }
    public function createUser(array $data): User|false|string
    {
        $firstname = $data['firstname'] ?? null;
        $lastname = $data['lastname'] ?? null;
        $email = $data['email'] ?? null;
        $adress = $data['adress'] ?? null;
        $password = $data['password'] ?? null;
        $confirmPassword = $data['confirm_password'] ?? null;

        if (!$firstname || !$lastname || !$email || !$password || !$confirmPassword) {
            return 'Missing required fields.';
        }

        if ($password !== $confirmPassword) {
            return 'Passwords do not match.';
        }

        $existingUser = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return 'User already exists.';
        }

        $user = new User();
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setEmail($email);
        $user->setAdress($adress);
        $user->setRoles(['ROLE_USER']);
        $user->setIsVerified(false);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function getUserById(int $id): ?User
    {
        return $this->em->getRepository(User::class)->find($id);
    }
    public function getUserByEmail(string $email): ?User
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return null;
        }
        return $user;
    }
    public function resetPassword(User $userData): ?array
    {
        $id = $userData['id'];
        $user = $this->userRepository->find($id);

        if (!$user) {
            return null;
        }

        $newPassword = bin2hex(random_bytes(4)); // 8 caractères aléatoires
        if ($this->changePassword($user, $newPassword)) {
            return ['new_password' => $newPassword];
        }

        return null;
    }

    private function changePassword(User $user, string $plainPassword): bool
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        try {
            $this->em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    // This method is used to update user data
    public function updateUserData(array $userData): ?User
    {
        $id = $userData['id'];
        $user = $this->userRepository->find($id);

        if (!$user) {
            return null;
        }

        // Update user properties
        if (isset($userData['firstname'])) {
            $user->setFirstname($userData['firstname']);
        }
        if (isset($userData['lastname'])) {
            $user->setLastname($userData['lastname']);
        }
        if (isset($userData['email'])) {
            $user->setEmail($userData['email']);
        }
        if (isset($userData['adress'])) {
            $user->setAdress($userData['adress']);
        }
        if (isset($userData['postalCode'])) {
            $user->setPostalCode($userData['postalCode']);
        }
        if (isset($userData['city'])) {
            $user->setCity($userData['city']);
        }

        // If password is provided, hash it and set it
        if (isset($userData['password']) && !empty($userData['password'])) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);
        }

        try {
            $this->em->flush();
            return $user;
        } catch (\Exception $e) {
            return null;
        }
    }

    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function changeRole($id, $role): array
    {
        $user = $this->userRepository->find($id);
        $user->setRoles($role);

        $this->em->persist($user);
        $this->em->flush();
        return $this->serializeUser($user);
    }

    private function serializeUser(User $user): array
    {

        return [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'role' => $user->getRoles(),
        ];

    }
    public function generateJWTvalidationToken(string $email): string
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return '';
        }

        return $this->jwtManager->create($user);
    }

    public function validateEmail(User $user, string $token): JsonResponse
    {
        $tokenValidation = $this->validateJwtToken($token);
        if (!$tokenValidation) {
            return new JsonResponse(['error' => 'INVALID JWT TOKEN'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        $payload = $this->jwtEncoder->decode($token);
        $id = $payload['id'];
        if(!$id){
            return new JsonResponse(['error' => 'INVALID ID'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        $newUser = $this->userRepository->find($id);
        if (!$newUser) {
            return new JsonResponse(['error' => 'INVALID ID'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        if ($newUser != $user) {
            return new JsonResponse(['error' => "USERS AREN'T MATCHING"], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $isVerified = $newUser->isVerified();
        if($isVerified){
            return new JsonResponse(['error'=>"This email is already verified"], 406);
        }

//        set user as is verified
        $user->setIsVerified(true);
        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse(['message' => 'Email is now validate'], 200);
    }
    private function validateJwtToken(string $token): bool
    {
        try {
            $payload = $this->jwtEncoder->decode($token);

            if (!isset($payload['exp']) || $payload['exp'] < time()) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

}
