<?php

namespace App\Entity;

use App\Entity\Traits\DateTimeTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\EnableTrait;
use App\Repository\ArticleRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: 'UNIQUE_ARTICLE_TITLE', fields: ['title'])]
#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[UniqueEntity(fields: ['title'], message: 'Ce titre à déjà été utilisé pour un autre article')]
class Article
{
    use EnableTrait,
        DateTimeTrait;
    # Les traits factorisent les propriétés et leur méthodes

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 100,
        # maxMessage: 'le titre ne doit pas dépasser {{ limit }} caractères',
        # there's allways a message by default
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 1000,
        maxMessage: 'le texte ne doit pas dépasser {{ limit }} caractères',
    )]
    private ?string $content = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }
}
