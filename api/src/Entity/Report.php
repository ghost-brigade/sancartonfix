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
use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['report_read']],
    denormalizationContext: ['groups' => ['report_write']],
)]
#[Get(
    normalizationContext: ['groups' => ['report_get', 'report_read']],
    security: 'is_granted("ROLE_ADMIN") or object.getRenting().getUser() == user',
    securityMessage: 'Access denied. You can only see your own reports or all reports if you are an admin.',
)]
#[GetCollection(
    normalizationContext: ['groups' => ['report_cget', 'report_read']],
    security: 'is_granted("ROLE_ADMIN") or object.getRenting().getUser() == user',
    securityMessage: 'Access denied. You can only see your own reports.',
)]
#[Post(
    denormalizationContext: ['groups' => ['report_post', 'report_write']],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Access denied. You can only create a report if you are a user.',
)]
#[Put(
    denormalizationContext: ['groups' => ['report_put', 'report_write']],
    security: 'is_granted("ROLE_ADMIN") or object.getRenting().getUser() == user',
    securityMessage: 'Access denied. You can only edit your own reports or all reports if you are an admin.',
)]
#[Delete(
    security: 'is_granted("ROLE_ADMIN") or object.getRenting().getUser() == user',
    securityMessage: 'Access denied. You can only delete your own reports or all reports if you are an admin.',
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'content' => 'word_start',
        'status' => 'exact',
        'renting' => 'exact',
    ]
)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['report_read'])]
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        minMessage: 'The name must be at least 3 characters long',
        max: 255,
        maxMessage: 'The name cannot be longer than 255 characters'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z ]+$/',
        message: 'The name can only contain letters and spaces'
    )]
    #[Groups(['report_read', 'report_write'])]
    #[ApiProperty(readable: true, writable: true, required: true, example: 'The box was dirty', description: 'The content of the report')]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['report_read', 'report_write'])]
    #[ApiProperty(readable: true, writable: true, required: true, example: true, description: 'The status of the report')]
    private ?bool $status = null;

    #[ORM\OneToOne(inversedBy: 'report', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['report_read', 'report_write'])]
    #[ApiProperty(readable: true, writable: true, required: true, example: '/api/rentings/1', description: 'The renting attached to the report')]
    private ?Renting $renting = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getRenting(): ?Renting
    {
        return $this->renting;
    }

    public function setRenting(Renting $renting): self
    {
        $this->renting = $renting;

        return $this;
    }
}
