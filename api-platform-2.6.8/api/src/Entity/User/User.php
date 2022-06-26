<?php

namespace App\Entity\User;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\User\CreateToken;
use App\Controller\User\CreateUser;
use App\Controller\User\UpdateUser;
use App\Repository\User\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

#[ApiResource(
    collectionOperations:[
        "get" => [
             "normalization_context" => ["groups" => ["GetUser"]],
            "denormalization_context" => ["groups" => ["SetUser"]],
        ],
        "post" => [
            "normalization_context" => ["groups" => ["GetUser"]],
            "denormalization_context" => ["groups" => ["SetUser"]],
            "controller" => CreateUser::class,
        ],
        "create_token" => [
            "method" => "patch",
            "controller" => CreateToken::class,
            'path' => '/users/create_token',
            "denormalization_context" => ["groups" => ["CreateToken"]]],
        "update_user" => [
            'method'=>'patch',
            "normalization_context" => ["groups" => ["GetUser"]],
            "controller" => UpdateUser::class,
            'path' => '/users/update_token',
            "denormalization_context" => ["groups" => ["SetUser"]],],
    ],
    itemOperations: [
        "get" => ["normalization_context" => ["groups" => ["GetUser"]],
            "denormalization_context" => ["groups" => ["SetUser"]],],
        "delete" => [],


    ]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('GetUser')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Email]
    #[Groups(['GetUser','CreateToken','SetUser'])]
    public $email;

    #[ORM\Column(type: 'string')]
    #[Groups(['GetUser','SetUser'])]
    public $roles = "user";

    #[ORM\Column(type: 'string')]
    #[Groups(['GetUser','SetUser'])]
    public $password;

    #[ORM\Column(type: 'text',nullable: 'true')]
    #[Groups(['GetUser'])]
    private string $token;

    #[ORM\Column(type: 'text',nullable: 'true')]
    #[Groups(['GetUser'])]
    private string $refresh_token;


    #[ORM\Column(type: "datetime", nullable: 'true')]
    #[Groups(['GetUser'])]
    public \DateTimeInterface $tokenCreateDate;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refresh_token;
    }


    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function settokenCreateDate(string $tokenCreateDate): self
    {
        $this->tokenCreateDate = new DateTimeImmutable();

        return $this;
    }

    public function setToken($token): self
    {
        $this->token = $token;

        return $this;
    }

    public function setRefreshToken($refresh_token): self
    {
        $this->refresh_token = $refresh_token;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): ?string
    {

        return $this->roles;
    }

    public function setRoles($roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
