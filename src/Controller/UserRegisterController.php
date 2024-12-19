<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\Gender;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserRegisterController  extends AbstractController
{
    #[Route('/users/register', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());
        $user = new User();
        $user->gender = Gender::MALE;
        $user->email = $data->email;
        $user->lastname = $data->lastname;
        $user->firstname = $data->firstname;
        $user->plainPassword = $data->plainPassword;
        $this->save($user);

        return new JsonResponse(['message' => 'User created'], 201);
    }
}