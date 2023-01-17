<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\CityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ApiResource(
    denormalizationContext: ['groups' => ['city_write']],
    normalizationContext: ['groups' => ['city_read']]
)]
#[Get(
    normalizationContext: ['groups' => ['city_get', 'city_read']],
)]
#[GetCollection(
    normalizationContext: ['groups' => ['city_cget', 'city_read']],
)]
#[Post(
    denormalizationContext:  ['groups' => ['city_post', 'city_write']],
    security: 'is_granted("ROLE_ADMIN")',
    securityMessage: 'Only admins can access this resource'
)]
#[Put(
    denormalizationContext:  ['groups' => ['city_put', 'city_write']],
    security: 'is_granted("ROLE_ADMIN")',
    securityMessage: 'Only admins can access this resource'
)]
#[Delete(
    security: 'is_granted("ROLE_ADMIN")',
    securityMessage: 'Only admins can delete cities')
]
class City
{
    
    #[ORM\Id]
    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Assert\Type(type: 'integer')]
    #[Groups(['city_read', 'city_write'])]
    #[ApiProperty(identifier: true, writable: true, readable: true)]
    private int $postalCode;

    #[ORM\Column(length: 255)]
    #[Groups(['city_read', 'city_write'])]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['city_read', 'city_write'])]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['city_read', 'city_write'])]
    private ?float $longitude = null;

    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    public function setPostalCode(int $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }
}
