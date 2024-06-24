<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM; # ORM becomes an alias

# #[Alias\Class] = Attributs PHP 8
# Always present in every entity
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Category
{
    #[ORM\Id] # Primary Key
    #[ORM\GeneratedValue] # Auto-increment
    #[ORM\Column]
    /* Lors de l'initialisation d'une propriété,
        avant que celle-ci ait une valeur, il faut l'initialiser à null
        c'est pourquoi ont lui permet le type null (?int, ?string, ?bool…) 
    */
    private ?int $id = null;

    #[ORM\Column(length: 255)] # varchar
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $enable = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updateAt = null;

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

    public function isEnable(): ?bool
    {
        return $this->enable;
    }

    public function setEnable(bool $enable): static
    {
        $this->enable = $enable;

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

    #[ORM\PrePersist]
    public function autoSetCreatedAt(): static
    {
        if (!$this->createdAt) {
            $this->createdAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeImmutable $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    #[ORM\PreUpdate]
    public function autoSetUpdateAt(): static
    {
        $this->updateAt = new \DateTimeImmutable();

        return $this;
    }
}
