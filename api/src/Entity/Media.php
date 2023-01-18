<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[UniqueEntity('name', message: 'There is already a media with this name')]
#[ApiResource(
    denormalizationContext: ['groups' => ['media_write']],
    normalizationContext: ['groups' => ['media_read']]
)]
#[Get(
    normalizationContext: ['groups' => ['media_get', 'media_read']],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Permission denied.'
)]
#[GetCollection(
    normalizationContext: ['groups' => ['media_cget', 'media_read']],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Permission denied.'
)]
#[Post(
    denormalizationContext:  ['groups' => ['media_post', 'media_write']],
    security: 'is_granted("ROLE_USER") and object.getHousing().getOwner() == user',
    securityMessage: 'Permission denied. You can only add media to your own housing.'
)]
#[Delete(
    security: 'is_granted("ROLE_USER") and object.getHousing().getOwner() == user',
    securityMessage: 'Permission denied. You can only delete media from your own housing.',
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'name' => 'exact',
    ]
)]
class Media
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['media_read'])]
    #[ApiProperty(identifier: true, writable: false, readable: true)]
    private $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_\-\.]+$/',
        message: 'The name can only contain letters, numbers, underscores, dashes and dots.'
    )]
    #[Groups(['media_read', 'media_write'])]
    #[ApiProperty(writable: true, readable: true, required: false, example: 'MyImage.jpg', description: 'The name of the media file.')]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Housing $housing = null;

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

    public function getHousing(): ?Housing
    {
        return $this->housing;
    }

    public function setHousing(?Housing $housing): self
    {
        $this->housing = $housing;

        return $this;
    }
}
