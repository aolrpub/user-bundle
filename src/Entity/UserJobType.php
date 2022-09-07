<?php

namespace Aolr\UserBundle\Entity;

use Aolr\UserBundle\Repository\UserJobTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_job_types")
 * @ORM\Entity(repositoryClass=UserJobTypeRepository::class)
 */
class UserJobType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="is_display", type="boolean", options={"default":1})
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
