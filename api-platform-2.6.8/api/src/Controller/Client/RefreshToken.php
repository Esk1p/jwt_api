<?php

namespace App\Controller\Client;

use App\Encoder\SomeEncoder;
use DateTimeZone;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\User\UserRepository;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use DateTimeImmutable;

class RefreshToken extends AbstractController
{
    public function __construct(
        private UserRepository $us,

    ) {}

    public function __invoke(Request $request, SomeEncoder $encoder,ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager();

        # Получение запроса в виде JSON
        $request = json_decode($request->getContent(),true);

        # Проверка на связь токенов
        if(mb_substr($request[0]['token'],-10,10) != mb_substr($request[0]['refresh_token'],-10,10)){
            return new JsonResponse(['MESSAGE'=>'Токены не связаны']);
        }

        # Костыль, который делает из объекта массив и расшифровывает токен
        $decode_token = json_decode(json_encode($encoder->decode($request[0]['token'])),true);

        # Получение пользователя, с email, из расшифровонного токена
        $data = $this->us->findEmail($decode_token[0]['email']);

        # Обновление даты создания токенов и создание нового access & refresh токена
        $data->tokenCreateDate = new DateTimeImmutable("",new DateTimeZone('Europe/Moscow'));

        $token = $encoder->encode(array($data));
        $refresh_token = $encoder->encode(array('I love bananas')).'.'.mb_substr($token,-10,10);

        $data->setToken($token);
        $data->setRefreshToken($refresh_token);
        $entityManager->flush();


        return new JsonResponse(['MESSAGE'=>'refresh & access tokens updated']);

    }

}
