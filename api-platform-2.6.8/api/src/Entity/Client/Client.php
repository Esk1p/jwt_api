<?php

namespace App\Entity\Client;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Client\CheckToken;
use App\Controller\Client\RefreshToken;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations:[
        "check_token" => [
            "method" => "patch",
            "controller" => CheckToken::class,
            'path' => '/clients/check_token',
            "denormalization_context" =>["groups" => ["Access"]]],

        "refresh_token"=>[
            "method" => "patch",
            "controller" =>RefreshToken::class,
            'path' => '/clients/refresh_token',
            "denormalization_context" =>["groups" => ["Refresh"]]
        ]

    ],
    itemOperations: []
)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    #[Groups(['Access','Refresh'])]
    public string $token = 'your_token';

    #[ORM\Column(type: 'text')]
    #[Groups('Refresh')]
    public string $refresh_token = 'your_refresh_token';
}
