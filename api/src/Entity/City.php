<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(
    normalizationContext: ['groups' => ['housing_read']],
    denormalizationContext: ['groups' => ['city_write']],
)]
#[GetCollection(
    normalizationContext: ['groups' => ['housing_cget', 'city_read']],
    // security: 'is_granted("ROLE_USER")',
    // securityMessage: 'You are not allowed to access this resource.',
)]
#[Get(
    normalizationContext: ['groups' => ['city_get', 'city_read']],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'You are not allowed to access this resource.',
)]
#[Post(
    denormalizationContext: ['groups' => ['city_post', 'city_write']],
    security: 'is_granted("ROLE_ADMIN")',
    securityMessage: 'You are not allowed to access this resource.',
)]
#[Put(
    denormalizationContext: ['groups' => ['city_put', 'city_write']],
    security: 'is_granted("ROLE_ADMIN")',
    securityMessage: 'You are not allowed to access this resource.',
)]
#[Delete(
    security: 'is_granted("ROLE_ADMIN")',
    securityMessage: 'You are not allowed to access this resource.',
)]
#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['housing_read', 'city_read'])]
    private $id = null;

    #[ORM\Column]
    #[Groups(['housing_read', 'city_read', 'city_write'])]
    private ?int $zipCode = null;

    #[ORM\Column(length: 255)]
    #[Groups(['housing_read', 'city_read', 'city_write'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'City', targetEntity: Housing::class)]
    private Collection $housings;

    public function __construct()
    {
        $this->housings = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    public function setZipCode(int $zipCode): self
    {
        $this->zipCode = $zipCode;

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

    /**
     * @return Collection<int, Housing>
     */
    public function getHousings(): Collection
    {
        return $this->housings;
    }

    public function addHousing(Housing $housing): self
    {
        if (!$this->housings->contains($housing)) {
            $this->housings->add($housing);
            $housing->setCity($this);
        }

        return $this;
    }

    public function removeHousing(Housing $housing): self
    {
        if ($this->housings->removeElement($housing)) {
            // set the owning side to null (unless already changed)
            if ($housing->getCity() === $this) {
                $housing->setCity(null);
            }
        }

        return $this;
    }
}
