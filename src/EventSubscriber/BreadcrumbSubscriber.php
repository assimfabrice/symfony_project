<?php

namespace App\EventSubscriber;
use App\Service\BreadcrumbService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class BreadcrumbSubscriber implements EventSubscriberInterface
{
    private BreadcrumbService $breadcrumbService;
    private RouterInterface $router;

    public function __construct(BreadcrumbService $breadcrumbService, RouterInterface $router)
    {
        $this->breadcrumbService = $breadcrumbService;
        $this->router = $router;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        // Ajoute la route "Home" par défaut
        $this->breadcrumbService->add('Home', 'app_document_index');


        // Récupère la route et ses paramètres
        $routeName = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params', []);

        // Ajoute la route courante au breadcrumb
        if ($routeName) {
            $this->breadcrumbService->add(ucfirst(str_replace('_', ' ', $routeName)), $routeName, $routeParams);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}