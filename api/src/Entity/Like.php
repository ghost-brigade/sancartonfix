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
use ApiPlatform\Metadata\Put;
use App\Repository\LikeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
#[ApiResource(
    denormalizationContext: ['groups' => ['like_write']],
    normalizationContext: ['groups' => ['like_read']]
)]
#[Get(
    normalizationContext: ['groups' => ['like_get', 'like_read']],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Permission denied.'
)]
#[GetCollection(
    normalizationContext: ['groups' => ['like_cget', 'like_read']],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Permission denied.'
)]
#[Post(
    denormalizationContext:  ['groups' => ['like_post', 'like_write']],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Permission denied.'
)]
#[Put(
    denormalizationContext:  ['groups' => ['like_put', 'like_write']],
    security: 'is_granted("ROLE_USER") and object.getAuthor() == user',
    securityMessage: 'Permission denied. You can only edit your own likes.',
)]
#[Delete(
    security: 'is_granted("ROLE_USER") and object.getAuthor() == user',
    securityMessage: 'Permission denied. You can only delete your own likes.',
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'liked' => 'exact',
        'housing' => 'exact',
        'author' => 'exact',
    ]
)]
class Like
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['like_read'])]
    #[ApiProperty(identifier: true, writable: false, readable: true)]
    private $id = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Type('boolean')]
    #[Assert\Choice([true, false])]
    #[Groups(['like_read', 'like_write'])]
    #[ApiProperty(writable: true, readable: true, required: true, example: true, description: 'Whether the user liked the housing or not.')]
    private ?bool $liked = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['like_read'])]
    #[ApiProperty(writable: false, readable: true, required: true, example: '/users/{id}', description: 'The user who liked the housing.')]
    #[Gedmo\Blameable(on: 'create')]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['like_read', 'like_write'])]
    #[ApiProperty(writable: true, readable: true, required: true, example: '/housings/{id}', description: 'The housing liked by the user.')]
    private ?Housing $housing = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function isLiked(): ?bool
    {
        return $this->liked;
    }

    public function setLiked(bool $liked): self
    {
        $this->liked = $liked;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

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
