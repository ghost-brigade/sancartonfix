<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email', message: 'There is already an account with this email')]
#[ApiResource(
    denormalizationContext: ['groups' => ['user_write']],
    normalizationContext: ['groups' => ['user_read']]
)]
#[Get(
    normalizationContext: ['groups' => ['user_get', 'user_read']],
    security: 'is_granted("ROLE_ADMIN") or object == user',
    securityMessage: 'Only admins or yourself can access your user details.'
)]
#[GetCollection(
    normalizationContext: ['groups' => ['user_cget', 'user_read']],
    security: 'is_granted("ROLE_ADMIN")',
    securityMessage: 'Only admins can access this resource.'
)]
/* Ajouter un systeme d'inscription a la place
#[Post(
    denormalizationContext:  ['groups' => ['user_post']],
    security: 'is_granted("ROLE_ADMIN")',
    securityMessage: 'Only admins can access this resource'
)]
*/
#[Patch(
    security: 'is_granted("ROLE_ADMIN") or object == user',
    securityMessage: 'Only admins can edit users or the user himself'
)]
#[Delete(
    security: 'is_granted("ROLE_ADMIN")',
    securityMessage: 'Only admins can delete users'
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'email' => 'exact',
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups('user_read')]
    #[ApiProperty(identifier: true, writable: false, readable: true)]
    private $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user_read', 'user_write'])]
    #[ApiProperty(writable: true, readable: true, example: 'user@mydomain.com', description: 'The email of the user')]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['user_read', 'user_write'])]
    #[ApiProperty(writable: true, readable: true, example: ['ROLE_USER'], description: 'The roles of the user', security: 'is_granted("ROLE_ADMIN")')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['user_read', 'user_write'])]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(
        min: 8, minMessage: 'Your password should be at least {{ limit }} characters',
        max: 200, maxMessage: 'Your password should not be more than {{ limit }} characters'
    )]
    #[ApiProperty(writable: true, readable: false, example: 'password', description: 'The password of the user', required: false)]

    private ?string $plainPassword = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
