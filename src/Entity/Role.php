<?php

namespace Aolr\UserBundle\Entity;

use Aolr\UserBundle\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="roles")
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 */
class Role
{
    const ROLE_SUPER_USER_ID            = 1;
    const ROLE_PUBLISHER_ID             = 2;
    const ROLE_MANAGING_EDITOR_ID       = 3;
    const ROLE_ASSISTANT_EDITOR_ID      = 4;

    const ROLE_SUPER_USER               = 'ROLE_SUPER_USER';

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
     * @ORM\Column(type="boolean", options={"default":1})
     */
    private $isActive = true;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $printableName;

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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getPrintableName(): ?string
    {
        return $this->printableName;
    }

    public function setPrintableName(string $printableName): self
    {
        $this->printableName = $printableName;

        return $this;
    }
}
