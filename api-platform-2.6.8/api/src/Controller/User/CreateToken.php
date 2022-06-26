<?php
namespace App\Controller\User;

use App\Encoder\SomeEncoder;
use DateTimeZone;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\User\UserRepository;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use DateTimeImmutable;

class CreateToken extends AbstractController
{
    public function __construct(
        private UserRepository $us,

    ) {}

    public function __invoke(Request $request, SomeEncoder $encoder,ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager();

        # Получение запроса в виде JSON
        $request = json_decode($request->getContent(),true);

        # Получение пользователя, с email, указанном в запросе
        $data = $this->us->findEmail($request[0]['email']);

        # Установка даты создания токена
        $data->tokenCreateDate = new DateTimeImmutable('',new DateTimeZone('Europe/Moscow'));

       if(!$data){
           throw new ConflictHttpException('Пользователя с таким email не сущствует');
        }

       # Создание токена из данных пользователя
        $token = $encoder->encode(array($data));

       $refresh_token = $encoder->encode(array('I love bananas')).'.'.mb_substr($token,-10,10);

        $data->setToken($token);
        $data->setRefreshToken($refresh_token);
        $entityManager->flush();



        return new JsonResponse(['MESSAGE'=>'tokens created']);

    }

}
