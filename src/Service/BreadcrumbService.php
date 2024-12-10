<?php

namespace App\Service;

class BreadcrumbService
{
    private array $breadcrumbs = [];

    public function add(string $label, ?string $route = null, array $routeParams = []): self
    {
        $this->breadcrumbs[] = [
            'label' => $label,
            'route' => $route,
            'params' => $routeParams,
        ];

        return $this;
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    public function reset(): self
    {
        $this->breadcrumbs = [];
        return $this;
    }
}