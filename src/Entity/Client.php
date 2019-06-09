<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 */
class Client
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Dialog", mappedBy="author")
     */
    private $dialogs;

    /**
     * @ORM\Column(type="string", length=180,  unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank()
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $middlename;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_application;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Applications", mappedBy="client", cascade={"remove"})
     */
    private $applications;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->dialogs = new ArrayCollection();
        $this->applications = new ArrayCollection();
    }

    /**
     * @return Collection|Dialog[]
     */
    public function getDialogs()
    {
        return $this->dialogs;
    }

    /**
     * @return Collection|Applications[]
     */
    public function getApplications()
    {
        return $this->applications;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getMiddlename(): ?string
    {
        return $this->middlename;
    }

    public function setMiddlename(?string $middlename): self
    {
        $this->middlename = $middlename;
        return $this;
    }

    public function getDateApplication(): ?\DateTimeInterface
    {
        return $this->date_application;
    }

    public function setDateApplication(\DateTimeInterface $date_application): self
    {
        $this->date_application = $date_application;
        return $this;
    }
}
