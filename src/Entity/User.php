<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /** Contraintes **/
    // Minimum et maximum caractères pour l'email
    #[Assert\Length(
        min: 6,
        max: 180,
        minMessage: 'Votre adresse email doit contenir minimum {{ limit }} caractères',
        maxMessage: 'Votre adresse email doit contenir maximum {{ limit }} caractères',
    )]
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    /** Contraintes **/
    // Doit comprendre entre 8 et 30 caractères
    #[Assert\Length(
        min: 8,
        max: 255,
        minMessage: 'Votre mot de passe doit contenir minimum {{ limit }} caractères',
        maxMessage: 'Votre mot de passe doit contenir maximum {{ limit }} caractères',
    )]
    #[ORM\Column(type: 'string')]
    private $password;

    /** Contraintes **/
    // Ne doit contenir que des lettres
    #[Assert\Type(
        type: 'alpha',
        message: 'IMPORTANT : Votre prénom ne doit pas contenir de caractères spéciaux',
    )]
    // Doit comprendre entre 2 et 25 caractères
    #[Assert\Length(
        min: 2,
        max: 25,
        minMessage: 'Votre prénom doit contenir minimum {{ limit }} caractères',
        maxMessage: 'Votre prénom doit contenir maximum {{ limit }} caractères',
    )]
    #[ORM\Column(type: 'string', length: 255)]
    private $firstname;

    /** Contraintes **/
    // Ne doit contenir que des lettres
    #[Assert\Type(
        type: 'alpha',
        message: 'IMPORTANT : Votre nom ne doit pas contenir de caractères spéciaux',
    )]
    // Doit comprendre entre 2 et 25 caractères
    #[Assert\Length(
        min: 2,
        max: 25,
        minMessage: 'Votre nom doit contenir minimum {{ limit }} caractères',
        maxMessage: 'Votre nom doit contenir maximum {{ limit }} caractères',
    )]
    #[ORM\Column(type: 'string', length: 255)]
    private $lastname;

    /** Contraintes **/
    // Doit comprendre exactement 9 caractères
    #[Assert\Length(
        min: 9,
        max: 9,
        exactMessage: 'Votre numéro de téléphone doit contenir 10 caractères ( Exemple: 0617xxxxxx )'
    )]
    #[ORM\Column(type: 'string')]
    private $phone;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Booking::class)]
    private $bookings;

    #[ORM\Column(type: 'boolean')]
    private $isBanned;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
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
        return (string)$this->email;
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

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setUser($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getUser() === $this) {
                $booking->setUser(null);
            }
        }

        return $this;
    }

    public function getIsBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;

        return $this;
    }
}
