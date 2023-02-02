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
use App\Controller\MediaController;
use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[UniqueEntity('filePath', message: 'There is already a media with this name')]
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
    inputFormats: ['multipart' => ['multipart/form-data']],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Permission denied. You can only add media to your own housing.',
    securityPostDenormalize: 'is_granted("ROLE_USER") and object.getHousing().getOwner() == user',
    securityPostDenormalizeMessage: 'You do not have permission to upload media to this housing.'
)]
#[Delete(
    security: 'is_granted("ROLE_USER") and object.getHousing().getOwner() == user',
    securityMessage: 'Permission denied. You can only delete media from your own housing.',
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
    ]
)]
class Media
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['media_read', 'housing_read'])]
    #[ApiProperty(identifier: true, writable: false, readable: true)]
    private $id = null;

    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    #[Groups(['media_read', 'renting_read', 'housing_read'])]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: "media", fileNameProperty: "filePath")]
    #[Assert\NotNull]
    #[Groups(['media_write'])]
    public ?File $file = null;

    #[ORM\Column(nullable: true)]
    public ?string $filePath = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['media_read', 'media_write'])]
    #[ApiProperty(writable: true, readable: true, required: true, description: 'The housing this media belongs to.', example: '/housings/{id}')]
    public ?Housing $housing = null;

    public function getId(): ?Uuid
    {
        return $this->id;
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
