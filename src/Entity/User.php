<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 120)]
    private ?string $firstname = null;

    #[ORM\Column(length: 120)]
    private ?string $lastname = null;

    #[ORM\Column]
    private ?bool $isActive = true;

    #[ORM\Column(length: 120)]
    private ?string $username = null;

    #[ORM\Column]
    private ?bool $isVerified = false;

    /**
     * @var Collection<int, Gallery>
     */
    #[ORM\OneToMany(targetEntity: Gallery::class, mappedBy: 'user')]
    private Collection $listGalleries;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    public function __construct()
    {
        $this->listGalleries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    // public function setUsername(string $username): static
    // {
    //     $this->username = $username;

    //     return $this;
    // }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Gallery>
     */
    public function getListGalleries(): Collection
    {
        return $this->listGalleries;
    }

    public function addListGallery(Gallery $listGallery): static
    {
        if (!$this->listGalleries->contains($listGallery)) {
            $this->listGalleries->add($listGallery);
            $listGallery->setUser($this);
        }

        return $this;
    }

    public function removeListGallery(Gallery $listGallery): static
    {
        if ($this->listGalleries->removeElement($listGallery)) {
            // set the owning side to null (unless already changed)
            if ($listGallery->getUser() === $this) {
                $listGallery->setUser(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    #[ORM\PrePersist]
    public function setUsername(): void
    {
        $firstnameFirstLetter = substr($this->firstname, 0, 1);
        $lastnameNFirstLetters = substr($this->lastname, 0, 7);
        $this->username = "{$firstnameFirstLetter}{$lastnameNFirstLetters}";
    }

    public function getFullName()
    {
        return "{$this->getFirstName()} {$this->getLastName()} ({$this->getUsername()})";
    }
}
