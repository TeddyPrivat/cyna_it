<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class UserController extends AbstractController
{
    #[Route('/users', name: 'app_user', methods: ['GET'])]
    public function getAllUsers(UserRepository $ur): Response
    {
        $users = $ur->findAll();
        $data = [];

        foreach($users as $user){
            $data[] = [
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail()
            ];
        }
        return $this->json($data);
    }
}
