<?php

namespace App\Controller\Client;

use App\Encoder\SomeEncoder;
use App\Entity\AccessLog\AccessLog;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CheckToken extends AbstractController
{


    public function __invoke(SomeEncoder $encoder,Request $request, ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager();
        $log = new AccessLog();

        # Получение запроса в виде JSON
        $request = json_decode($request->getContent(),true);
        $log->setToken($request[0]['token']);


        # Костыль, который делает из объекта массив и расшифровывает токен
        $decode_token = json_decode(json_encode($encoder->decode($request[0]['token'])),true);

        # Создание объекта c датой создания токена из части массива раздекодированного токена
        $token_create_date = new DateTimeImmutable($decode_token[0]['tokenCreateDate']['date'],new DateTimeZone('Europe/Moscow'));



        # Проверка действительность токена по времени
        $date_now = new DateTimeImmutable("",new DateTimeZone('Europe/Moscow'));
        $log->setLogDate($date_now);

        $date_check = $token_create_date->diff($date_now);

        $date_difference = $date_check->format('%R%a');

        if ($date_difference >= 1){
            $log->setStatus('Failed');
            $entityManager->persist($log);
            $entityManager->flush();
            return new JsonResponse(['MESSAGE' => 'Токен просрочен']);
        }


        #Проверка роли
        if ($decode_token[0]['roles'] == 'admin'){
            $log->setStatus('OK');
            $entityManager->persist($log);
            $entityManager->flush();
            return new JsonResponse(['Message'=>'Получены права admin']);
        }

        $log->setStatus('OK');
        $entityManager->persist($log);
        $entityManager->flush();
        return new JsonResponse(['Message'=>'Получены права user']);

    }


}
