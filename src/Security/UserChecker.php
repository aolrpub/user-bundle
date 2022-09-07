<?php

namespace Aolr\UserBundle\Security;

use Aolr\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return ;
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return ;
        }

        if (!$user->getIsVerified()) {
            throw new CustomUserMessageAccountStatusException('NOT_VERIFIED');
        }
    }
}
