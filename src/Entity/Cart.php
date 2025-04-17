<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $product = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $service = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantityProduct = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantityService = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getProduct(): ?array
    {
        return $this->product;
    }

    public function setProduct(?array $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getService(): ?array
    {
        return $this->service;
    }

    public function setService(?array $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getQuantityProduct(): ?int
    {
        return $this->quantityProduct;
    }

    public function setQuantityProduct(?int $quantityProduct): static
    {
        $this->quantityProduct = $quantityProduct;

        return $this;
    }

    public function getQuantityService(): ?int
    {
        return $this->quantityService;
    }

    public function setQuantityService(?int $quantityService): static
    {
        $this->quantityService = $quantityService;

        return $this;
    }
}
