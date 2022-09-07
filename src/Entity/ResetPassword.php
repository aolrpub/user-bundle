<?php

namespace Aolr\UserBundle\Entity;

use Aolr\UserBundle\Repository\ResetPasswordRepository;
use Doctrine\ORM\Mapping as ORM;
use Aolr\UserBundle\Entity\User;

/**
 * @ORM\Table(name="reset_passwords", indexes={@ORM\Index(name="idx_hash_key", columns={"hash_key"})})
 * @ORM\Entity(repositoryClass=ResetPasswordRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class ResetPassword
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $hashKey;

    /**
     * @ORM\Column(type="boolean", options={"default":1})
     */
    private $isValid = true;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getHashKey(): ?string
    {
        return $this->hashKey;
    }

    public function setHashKey(string $hashKey): self
    {
        $this->hashKey = $hashKey;

        return $this;
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate(): \DateTime
    {
        return $this->createdDate;
    }

    /**
     * @param mixed $createdDate
     *
     * @return ResetPassword
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdDate = new \DateTime();
    }

}
