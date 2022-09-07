<?php

namespace Aolr\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="email_codes")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class EmailCode
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $code;

    /**
     * @var int
     * @ORM\Column(type="smallint", options={"default":"0"})
     */
    private $sendTimes = 0;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return EmailCode
     */
    public function setEmail(string $email): EmailCode
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     *
     * @return EmailCode
     */
    public function setCode(int $code): EmailCode
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return int
     */
    public function getSendTimes(): int
    {
        return $this->sendTimes;
    }

    /**
     * @param int $sendTimes
     *
     * @return EmailCode
     */
    public function setSendTimes(int $sendTimes): EmailCode
    {
        $this->sendTimes = $sendTimes;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreateAt(): \DateTime
    {
        return $this->createAt;
    }

    /**
     * @param \DateTime $createAt
     *
     * @return EmailCode
     */
    public function setCreateAt(\DateTime $createAt): EmailCode
    {
        $this->createAt = $createAt;
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createAt = new \DateTime();
    }
}
