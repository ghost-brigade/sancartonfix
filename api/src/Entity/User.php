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
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
#[Post(
    denormalizationContext:  ['groups' => ['user_post', 'user_write']],
)]
#[Put(
    denormalizationContext: ['groups' => ['user_put', 'user_write']],
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
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id',
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

    #[Groups(['user_write'])]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(
        min: 8, minMessage: 'Your password should be at least {{ limit }} characters',
        max: 200, maxMessage: 'Your password should not be more than {{ limit }} characters'
    )]
    #[ApiProperty(writable: true, readable: false, example: 'password', description: 'The password of the user', required: false)]
    private ?string $plainPassword = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Housing::class)]
    private Collection $housings;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Like::class)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Renting::class)]
    private Collection $rentings;

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2, minMessage: 'Your firstname should be at least {{ limit }} characters',
        max: 255, maxMessage: 'Your firstname should not be more than {{ limit }} characters'
    )]
    #[Groups(['user_read', 'user_write'])]
    #[ApiProperty(writable: true, readable: true, example: 'Julien', description: 'The firstname of the user')]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2, minMessage: 'Your lastname should be at least {{ limit }} characters',
        max: 255, maxMessage: 'Your lastname should not be more than {{ limit }} characters'
    )]
    #[Groups(['user_read', 'user_write'])]
    #[ApiProperty(writable: true, readable: true, example: 'Bécile', description: 'The lastname of the user')]
    private ?string $lastname = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Type('boolean')]
    #[Assert\Choice([true, false])]
    #[Groups(['user_read', 'user_write'])]
    #[ApiProperty(writable: true, readable: true, example: 'true', description: 'The gender of the user')]
    private ?bool $gender = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero(
        message: 'Your balance should be positive or zero'
    )]
    #[Groups(['user_read', 'user_write'])]
    #[ApiProperty(writable: true, readable: true, example: 100.0, description: 'The balance of the user', securityPostDenormalize: 'is_granted("ROLE_ADMIN")')]
    private ?float $balance = 0.0;

    public function __construct()
    {
        $this->housings = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->rentings = new ArrayCollection();
    }

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

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

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
            $housing->setOwner($this);
        }

        return $this;
    }

    public function removeHousing(Housing $housing): self
    {
        if ($this->housings->removeElement($housing)) {
            // set the owning side to null (unless already changed)
            if ($housing->getOwner() === $this) {
                $housing->setOwner(null);
            }
        }

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
            $like->setAuthor($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getAuthor() === $this) {
                $like->setAuthor(null);
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
            $renting->setClient($this);
        }

        return $this;
    }

    public function removeRenting(Renting $renting): self
    {
        if ($this->rentings->removeElement($renting)) {
            // set the owning side to null (unless already changed)
            if ($renting->getClient() === $this) {
                $renting->setClient(null);
            }
        }
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function isGender(): ?bool
    {
        return $this->gender;
    }

    public function setGender(bool $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(?float $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

}
