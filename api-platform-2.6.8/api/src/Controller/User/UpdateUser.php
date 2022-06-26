<?php

namespace App\Controller\User;

use App\Encoder\SomeEncoder;
use App\Repository\User\UserRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UpdateUser extends AbstractController
{
    public function __construct(
        private UserRepository $us,

    ) {}
    public function __invoke(Request $request,ManagerRegistry $registry, SomeEncoder $encoder){

        $entityManager = $registry->getManager();
        $request = json_decode($request->getContent(),true);

        $data = $this->us->findEmail($request[0]['email']);

        if(!$data){
            return new JsonResponse(['Message'=>'Такого пользователя не существует']);
        }



        if($request[0]['roles'] != 'admin' && $request[0]['roles'] != 'user'){
            return new JsonResponse(['MESSAGE'=>'Невозможно задать такую роль']);
        }


        $data->tokenCreateDate = new DateTimeImmutable('',new DateTimeZone('Europe/Moscow'));
        $data->setRoles($request[0]['roles']);
        $data->setPassword($request[0]['password']);

        $token = $encoder->encode(array($data));
        $refresh_token = $encoder->encode(array('I love bananas')).'.'.mb_substr($token,-10,10);
        $data->setToken($token);
        $data->setRefreshToken($refresh_token);

        $entityManager->flush();

        return new JsonResponse(['Message'=>'Данные пользователя обновлены']);

    }
}
