<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getUser"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getUser"])]
    #[Assert\Length(min: 1, max: 50, minMessage: "Le prenom doit faire au moins {{ limit }} caractères", maxMessage: "Le prenom ne peut pas faire plus de {{ limit }} caractères")]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getUser"])]
    #[Assert\Length(min: 1, max: 50, minMessage: "Le nom doit faire au moins {{ limit }} caractères", maxMessage: "Le nom ne peut pas faire plus de {{ limit }} caractères")]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getUser"])]
    #[Assert\NotBlank(message: "L'adresse mail est obligatoire")]
    #[Assert\Length(min: 5, max: 255, minMessage: "Le mail doit faire au moins {{ limit }} caractères", maxMessage: "Le mail ne peut pas faire plus de {{ limit }} caractères")]
    private ?string $email = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(["getUser"])]
    private ?Customer $customer = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

}
