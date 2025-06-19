<?php

namespace App\Entity;

use AllowDynamicProperties;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[AllowDynamicProperties]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[Groups(['product:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['product:read'])]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Groups(['product:read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Groups(['product:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imgUrl = null;

    #[Groups(['product:read'])]
    #[ORM\Column]
    private ?float $price = null;

    #[Groups(['product:read'])]
    #[ORM\Column]
    private ?int $stock = null;

    #[Groups(['product:read'])]
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products')]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getImgUrl(): ?string { return $this->imgUrl; }

    public function setImgUrl(?string $imgUrl): static
    {
        $this->imgUrl = $imgUrl;
        return $this;
    }

    public function getPrice(): ?float { return $this->price; }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getStock(): ?int { return $this->stock; }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;
        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection { return $this->categories; }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);
        return $this;
    }
}
