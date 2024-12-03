<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['product:read']],
    denormalizationContext: ['groups' => ['product:write']],
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:read', 'product:write'])]
    #[Assert\NotBlank(message: "Product name is required.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Product name must be at least {{ limit }} characters long.",
        maxMessage: "Product name cannot exceed {{ limit }} characters.",
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:read', 'product:write'])]
    #[Assert\NotBlank(message: "Product description is required.")]
    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: "Description must be at least {{ limit }} characters long.",
        maxMessage: "Description cannot exceed {{ limit }} characters.",
    )]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['product:read', 'product:write'])]
    #[Assert\NotNull(message: "Price is required.")]
    #[Assert\Positive(message: "Price must be a positive value.")]
    private ?float $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['product:read'])]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['product:read', 'product:write'])]
    #[Assert\NotNull(message: "Category is required.")]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
