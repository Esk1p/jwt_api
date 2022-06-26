<?php

namespace App\Controller\User;

use App\Entity\User\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CreateUser extends AbstractController
{
    public function __invoke(Request $request,ManagerRegistry $registry){
        $data = new User;
        $entityManager = $registry->getManager();
        $request = json_decode($request->getContent(),true);

       if($request['roles'] != 'admin' && $request['roles'] != 'user'){
            return new JsonResponse(['MESSAGE'=>'Невозможно создать пользователя с такой ролью']);
       }

        $data->setEmail($request['email']);
        $data->setPassword($request['password']);
        $data->setRoles($request['roles']);

        $entityManager->persist($data);
        $entityManager->flush();

        return new JsonResponse(['Message'=>'Пользователь создан']);


    }
}
