<?php

namespace Aolr\UserBundle\EventSubscriber;

use App\Event\Event;
use Aolr\FeedBundle\Service\FeedManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    /**
     * @var FeedManager
     */
    private $feedManager;

    public function __construct(FeedManager $feedManager)
    {
        $this->feedManager = $feedManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
            LogoutEvent::class => 'onLogout',
            SwitchUserEvent::class => 'onSwitchUser'
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event)
    {
        $this->feedManager->save(Event::EVENT_LOGIN);
    }

    public function onLogout(LogoutEvent $event)
    {
        $this->feedManager->save(Event::EVENT_LOGOUT);
    }

    public function onSwitchUser(SwitchUserEvent $event)
    {
        $this->feedManager->save(Event::EVENT_SWITCH_USER, [
            't_un' => $event->getTargetUser()->getName(),
            't_em' => $event->getTargetUser()->getEmail()
        ]);
    }
}
