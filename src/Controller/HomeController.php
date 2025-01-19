<?php
namespace App\Controller;

use App\Service\Router;
use App\Service\Templating;

class HomeController
{
    private Router $router;
    private Templating $templating;

    public function __construct(Router $router, Templating $templating)
    {
        $this->router = $router;
        $this->templating = $templating;
    }

    public function indexAction(): string
    {
        return $this->templating->render("home/index.html.php", [
            'router' => $this->router,
        ]);
    }
}