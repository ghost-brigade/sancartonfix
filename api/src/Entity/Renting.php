<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\RentingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: RentingRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['renting_read']],
    denormalizationContext: ['groups' => ['renting_write']],
)]
#[GetCollection(
    normalizationContext: ['groups' => ['renting_cget', 'renting_read']],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'You are not allowed to access this resource.',
)]
#[Get(
    normalizationContext: ['groups' => ['renting_get', 'renting_read']],
    security: 'is_granted("ROLE_USER") and object.getClient() == user',
    securityMessage: 'You are not allowed to access this resource.',
)]
#[Post(
    denormalizationContext: ['groups' => ['renting_post', 'renting_write']],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'You are not allowed to access this resource.',
)]
#[Put(
    denormalizationContext: ['groups' => ['renting_put', 'renting_write']],
    security: 'is_granted("ROLE_ADMIN")',
    securityMessage: 'You are not allowed to access this resource.',
)]
#[Delete(
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'You are not allowed to access this resource.',
)]
class Renting
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['renting_read'])]
    #[ApiProperty(identifier: true)]
    private $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Groups(['renting_read', 'renting_write', 'housing_read'])]
    #[ApiProperty(example: '2021-01-01', openapiContext: ['format' => 'date'], readable: true, writable: true, required: true)]
    private ?\DateTimeImmutable $dateStart = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Groups(['renting_read', 'renting_write', 'housing_read'])]
    #[ApiProperty(example: '2021-01-02', openapiContext: ['format' => 'date'], readable: true, writable: true, required: true)]
    private ?\DateTimeImmutable $dateEnd = null;

    #[ORM\ManyToOne(inversedBy: 'rentings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['renting_read'])]
    #[ApiProperty(readable: true, writable: false, required: true, example: '/api/users/{id}')]
    #[Gedmo\Blameable(on: 'create')]
    private ?User $client = null;

    #[ORM\ManyToOne(inversedBy: 'rentings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['renting_read', 'renting_write'])]
    #[ApiProperty(readable: true, writable: true, required: true, example: '/api/housing/{id}')]
    private ?Housing $housing = null;

    #[ORM\OneToOne(mappedBy: 'renting', cascade: ['persist', 'remove'])]
    private ?Report $report = null;

    #[ORM\Column(nullable: true)]
    private ?bool $status = false;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getDateStart(): ?\DateTimeImmutable
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeImmutable $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeImmutable
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeImmutable $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getHousing(): ?Housing
    {
        return $this->housing;
    }

    public function setHousing(?Housing $housing): self
    {
        $this->housing = $housing;

        return $this;
    }

    public function getReport(): ?Report
    {
        return $this->report;
    }

    public function setReport(Report $report): self
    {
        // set the owning side of the relation if necessary
        if ($report->getRenting() !== $this) {
            $report->setRenting($this);
        }

        $this->report = $report;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
