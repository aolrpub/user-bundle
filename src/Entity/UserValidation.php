<?php

namespace Aolr\UserBundle\Entity;

use Aolr\UserBundle\Repository\UserValidationRepository;
use Doctrine\ORM\Mapping as ORM;
use Aolr\UserBundle\Entity\User;

/**
 * @ORM\Table(name="user_validations", indexes={@ORM\Index(name="idx_hash_key", columns={"hash_key"})})
 * @ORM\Entity(repositoryClass=UserValidationRepository::class)
 *
 * @ORM\HasLifecycleCallbacks
 */
class UserValidation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $hashKey;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDate;

    /**
     * @ORM\Column(type="boolean", options={"default":1})
     */
    private $isValid = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
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


    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param mixed $createdDate
     *
     * @return UserValidation
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * @param mixed $isValid
     *
     * @return UserValidation
     */
    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePrepersist()
    {
        $this->createdDate = new \DateTime();
    }
}
