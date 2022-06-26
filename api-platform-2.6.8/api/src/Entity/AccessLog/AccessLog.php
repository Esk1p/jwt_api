<?php

namespace App\Entity\AccessLog;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations:[
    "get" => []],
    itemOperations:[
    "get" => []]

)]

#[ORM\Entity]
#[ORM\Table(name: '`access_log`')]
class AccessLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    public string $token;

    #[ORM\Column(type: "datetime")]
    public \DateTimeInterface $login_date;

    #[ORM\Column(type: 'string')]
    public string $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setLogDate(\DateTimeInterface $login_date): self
    {
        $this->login_date = $login_date;

        return $this;
    }


}
