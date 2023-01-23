<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['category_read']],
    denormalizationContext: ['groups' => ['category_write']],
)]
#[Get(
    normalizationContext: ['groups' => ['category_get', 'category_read']],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Access denied.',
)]
#[GetCollection(
    normalizationContext: ['groups' => ['category_cget', 'category_read']],
    //security: 'is_granted("ROLE_USER")',
    //securityMessage: 'Access denied.',
)]
#[Post(
    denormalizationContext: ['groups' => ['category_post', 'category_write']],
    security: 'is_granted("ROLE_ADMIN")',
    securityMessage: 'Access denied.',
)]
#[Put(
    denormalizationContext: ['groups' => ['category_put', 'category_write']],
    security: 'is_granted("ROLE_ADMIN")',
    securityMessage: 'Access denied.',
)]
#[Delete(
    security: 'is_granted("ROLE_ADMIN")',
    securityMessage: 'Access denied.',
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'name' => 'word_start',
        'slug' => 'exact',
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id',
        'name',
        'slug',
    ],
)]
class Category
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['category_read', 'housing_read'])]
    #[ApiProperty(identifier: true)]
    private $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['category_read', 'category_write', 'housing_read'])]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3, minMessage: 'The name must be at least 3 characters long',
        max: 255, maxMessage: 'The name cannot be longer than 255 characters',
    )]
    #[ApiProperty(readable: true, writable: true, example: 'Category name', required: true)]
    private ?string $name = null;

    #[ORM\Column(length: 128, unique: true)]
    #[Groups(['category_read'])]
    #[Gedmo\Slug(fields: ['name'], unique: true)]
    #[ApiProperty(identifier: true, readable: true, writable: false)]
    private $slug;

    #[ORM\OneToMany(mappedBy: 'Category', targetEntity: Housing::class)]
    private Collection $housings;

    public function __construct()
    {
        $this->housings = new ArrayCollection();
    }

    public function getId(): ?Uuid
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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
            $housing->setCategory($this);
        }

        return $this;
    }

    public function removeHousing(Housing $housing): self
    {
        if ($this->housings->removeElement($housing)) {
            // set the owning side to null (unless already changed)
            if ($housing->getCategory() === $this) {
                $housing->setCategory(null);
            }
        }

        return $this;
    }
}
