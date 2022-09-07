<?php

namespace Aolr\UserBundle\Entity\Traits;

trait UserTrait
{
    public function getName()
    {
        return ($this->firstname ? ($this->firstname . ' ') : '') . $this->lastname;
    }

    public function getNameWithEmail()
    {
        return $this->getName() . ' <' . $this->email . '>';
    }

    public function getLastnameWithTitle()
    {
        return $this->title . ' ' . $this->lastname;
    }
}
