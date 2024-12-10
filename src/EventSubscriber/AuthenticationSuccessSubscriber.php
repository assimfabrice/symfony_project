<?php

namespace App\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class AuthenticationSuccessSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly RouterInterface $router,
    )
    {
    }

    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {


        if (in_array('ROLE_ADMIN', $this->security->getUser()->getRoles(), true)) {
            // Si l'utilisateur a le rôle ADMIN, on le redirige
            $url = $this->router->generate('app_document_index'); // Changez 'admin_dashboard' par votre route

            $event->setResponse(new RedirectResponse($url));

        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccessEvent',
        ];
    }
}
