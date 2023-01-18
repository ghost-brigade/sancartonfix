<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\HousingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HousingRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['housing_read']],
    denormalizationContext: ['groups' => ['housing_write']],
)]
#[GetCollection(
    normalizationContext: ['groups' => ['housing_cget', 'housing_read']],
    security: 'is_granted("ROLE_ADMIN") or object.getOwner() == user',
    securityMessage: 'You are not allowed to access this resource.',
)]
#[Get(
    normalizationContext: ['groups' => ['housing_get', 'housing_read']],
    security: 'is_granted("ROLE_ADMIN") or object.getOwner() == user',
    securityMessage: 'You are not allowed to access this resource.',
)]
#[Post(
    denormalizationContext: ['groups' => ['housing_post', 'housing_write']],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'You are not allowed to access this resource.',
)]
#[Put(
    denormalizationContext: ['groups' => ['housing_put', 'housing_write']],
    security: 'is_granted("ROLE_ADMIN") or object.getOwner() == user',
    securityMessage: 'You are not allowed to access this resource.',
)]
// todo check if deletion is authorized (recredit money to user)
#[Delete(
    security: 'is_granted("ROLE_ADMIN") or object.getOwner() == user',
    securityMessage: 'You are not allowed to access this resource.',
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'name' => 'word_start',
        'latitude' => 'exact',
        'longitude' => 'exact',
        'category.name' => 'exact',
        'owner' => 'exact',
    ]
)]
#[ApiFilter(
    NumericFilter::class,
    properties: [
        'price',
    ]
)]
#[ApiFilter(
    RangeFilter::class,
    properties: [
        'price',
        'likes',
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'createdAt' => 'DESC',
    ],
)]
#[ApiFilter(
    DateFilter::class,
    properties: [
        'createdAt',
    ]
)]
class Housing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['housing_read'])]
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3, minMessage: 'The name must be at least 3 characters long',
        max: 255, maxMessage: 'The name cannot be longer than 255 characters'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z ]+$/',
        message: 'The name can only contain letters and spaces'
    )]
    #[Groups(['housing_read', 'housing_write'])]
    #[ApiProperty(required: true, readable: true, writable: true, example: 'My housing')]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3, minMessage: 'The description must be at least 3 characters long',
        max: 600, maxMessage: 'The description cannot be longer than 600 characters'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9 ]+$/',
        message: 'The description can only contain letters, numbers and spaces'
    )]
    #[Groups(['housing_read', 'housing_write'])]
    #[ApiProperty(required: true, readable: true, writable: true, example: 'My housing description')]
    private ?string $description = null;

    #[ORM\Column(nullable: true, type: 'decimal', precision: 10, scale: 8)]
    #[Assert\NotBlank]
    #[Assert\Range(
        min: -90, max: 90,
        notInRangeMessage: 'The latitude must be between -90 and 90',
    )]
    #[Assert\Type(type: 'float', message: 'The latitude must be a float')]
    #[Groups(['housing_read', 'housing_write'])]
    #[ApiProperty(required: true, readable: true, writable: true, example: 48.856614)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true, type: 'decimal', precision: 11, scale: 8)]
    #[Assert\NotBlank]
    #[Assert\Range(
        min: -90, max: 90,
        notInRangeMessage: 'The latitude must be between -90 and 90',
    )]
    #[Assert\Type(type: 'float', message: 'The latitude must be a float')]
    #[Groups(['housing_read', 'housing_write'])]
    #[ApiProperty(required: true, readable: true, writable: true, example: 48.856614)]
    private ?float $longitude = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Range(
        min: 0, max: 1000000,
        notInRangeMessage: 'The price must be between 0 and 1000000',
    )]
    #[Assert\Type(type: 'float', message: 'The price must be a float')]
    #[Assert\PositiveOrZero(message: 'The price must be greater than 0')]
    #[Groups(['housing_read', 'housing_write'])]
    #[ApiProperty(required: true, readable: true, writable: true, example: 100)]
    private ?float $price = null;

    #[ORM\Column]
    #[Groups(['housing_read', 'housing_write'])]
    #[ApiProperty(readable: true, writable: true, example: true, securityPostDenormalize: 'is_granted("ROLE_ADMIN")')]
    private ?bool $active = null;

    #[ORM\ManyToOne(inversedBy: 'housings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['housing_read', 'housing_write'])]
    #[ApiProperty(readable: true, writable: true, example: '/api/users/{id}', securityPostDenormalize: 'is_granted("ROLE_ADMIN")')]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'housings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['housing_read', 'housing_write'])]
    #[ApiProperty(readable: true, writable: true, example: '/api/categories/{id}', securityPostDenormalize: 'is_granted("ROLE_ADMIN")')]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'housing', targetEntity: Like::class)]
    #[Groups(['housing_read'])]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'housing', targetEntity: Media::class)]
    #[Groups(['housing_read'])]
    private Collection $media;

    #[ORM\OneToMany(mappedBy: 'housing', targetEntity: Renting::class)]
    #[Groups(['housing_read'])]
    private Collection $rentings;

    #[ORM\Column]
    #[Groups(['housing_read'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->likes = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->rentings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setHousing($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getHousing() === $this) {
                $like->setHousing(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): self
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->setHousing($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getHousing() === $this) {
                $medium->setHousing(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Renting>
     */
    public function getRentings(): Collection
    {
        return $this->rentings;
    }

    public function addRenting(Renting $renting): self
    {
        if (!$this->rentings->contains($renting)) {
            $this->rentings->add($renting);
            $renting->setHousing($this);
        }

        return $this;
    }

    public function removeRenting(Renting $renting): self
    {
        if ($this->rentings->removeElement($renting)) {
            // set the owning side to null (unless already changed)
            if ($renting->getHousing() === $this) {
                $renting->setHousing(null);
            }
        }

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

}