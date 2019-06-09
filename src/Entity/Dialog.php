<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * @ORM\Entity(repositoryClass="App\Repository\DialogRepository")
 */
class Dialog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DialogMessages", mappedBy="dialog")
     */
    private $messages;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="dialogs")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="dialogs")
     * @ORM\JoinColumn(nullable=true)
     */
    private $operator;

    /**
     * @ORM\Column(type="datetime")
     */
    private $time_create;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    /**
     * @return Collection|DialogMessages[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return mixed
     */
    public function getAuthor(): ?Client
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthorId(Client $author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getOperator(): ?User
    {
        return $this->operator;
    }

    /**
     * @param mixed $operator
     */
    public function setOperatorId(?User $operator)
    {
        $this->operator = $operator;
    }

    public function getTimeCreate(): ?\DateTimeInterface
    {
        return $this->time_create;
    }

    public function setTimeCreate(\DateTimeInterface $time_create): self
    {
        $this->time_create = $time_create;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
}
