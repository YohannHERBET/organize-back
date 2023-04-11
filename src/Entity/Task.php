<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_task", "get_all"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"get_task", "get_all"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(min=0, max=3)
     * @Groups({"get_task", "get_all"})
     */
    private $priority;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"get_task", "get_all"})
     */
    private $descritpionTask;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get_all"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"get_task", "get_all"})
     */
    private $startingDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @ORM\OrderBy({"dueDate" = "DESC"})
     * @Groups({"get_task", "get_all"})
     */
    private $dueDate;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="tasks")
     * @Groups({"get_for_task"})
     */
    private $category;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getDescritpionTask(): ?string
    {
        return $this->descritpionTask;
    }

    public function setDescritpionTask(?string $descritpionTask): self
    {
        $this->descritpionTask = $descritpionTask;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStartingDate(): ?\DateTimeInterface
    {
        return $this->startingDate;
    }

    public function setStartingDate(?\DateTimeInterface $startingDate): self
    {
        $this->startingDate = $startingDate;

        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
