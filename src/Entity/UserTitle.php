<?php

namespace Aolr\UserBundle\Entity;

use Aolr\UserBundle\Repository\UserTitleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_titles")
 * @ORM\Entity(repositoryClass=UserTitleRepository::class)
 */
class UserTitle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", options={"default":1})
     */
    private $isDisplay = true;

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

    public function getIsDisplay(): ?bool
    {
        return $this->isDisplay;
    }

    public function setIsDisplay(bool $isDisplay): self
    {
        $this->isDisplay = $isDisplay;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
