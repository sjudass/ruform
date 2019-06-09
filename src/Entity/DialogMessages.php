<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\DialogMessagesRepository")
 *
 */
class DialogMessages
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_operator;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Dialog", inversedBy="messages")
     * @ORM\JoinColumn(nullable=true)
     */
    private $dialog;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $message_text;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $author_ip;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_read;

    /**
     * @ORM\Column(type="datetime")
     */
    private $message_time;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessageText(): ?string
    {
        return $this->message_text;
    }

    public function setMessageText(string $message_text): self
    {
        $this->message_text = $message_text;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDialog(): Dialog
    {
        return $this->dialog;
    }

    /**
     * @param mixed $dialog
     */
    public function setDialogId(Dialog $dialog)
    {
        $this->dialog = $dialog;
    }

    public function getIsOperator(): ?bool
    {
        return $this->is_operator;
    }

    public function setIsOperator(bool $is_operator): self
    {
        $this->is_operator = $is_operator;
        return $this;
    }

    public function getAuthorIp(): ?string
    {
        return $this->author_ip;
    }

    public function setAuthorIp(string $author_ip): self
    {
        $this->author_ip = $author_ip;
        return $this;
    }

    public function getIsRead(): ?bool
    {
        return $this->is_read;
    }

    public function setIsRead(bool $is_read): self
    {
        $this->is_read = $is_read;
        return $this;
    }

    public function getMessageTime(): ?\DateTimeInterface
    {
        return $this->message_time;
    }

    public function setMessageTime(\DateTimeInterface $message_time): self
    {
        $this->message_time = $message_time;
        return $this;
    }
}
