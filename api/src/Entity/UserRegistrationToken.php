<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Controller\User\RegisterValidationController;
use App\Repository\UserRegistrationTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRegistrationTokenRepository::class)]
#[ApiResource(
    denormalizationContext: ['groups' => ['user_registration_token_write']],
    normalizationContext: ['groups' => ['user_registration_token_read']]
)]
#[Get(
    normalizationContext: [
        'groups' => ['user_registration_token_get', 'user_registration_token_read']
    ],
    //security: 'object.getAccount() == user',
    //securityMessage: 'Only the user himself can access his registration token.',
    uriTemplate: '/users/validate_account/{token}',
    controller: RegisterValidationController::class,
)]
class UserRegistrationToken
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[ApiProperty(identifier: false)]
    private $id = null;

    #[ORM\Column(length: 255)]
    #[ApiProperty(identifier: true)]
    private ?string $token = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $account = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column]
    private ?bool $active;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->active    = false;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getAccount(): ?User
    {
        return $this->account;
    }

    public function setAccount(?User $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
