<?php
namespace App\Controller;

use App\Service\Router;
use App\Service\Templating;

class ScraperController
{
    private Router $router;
    private Templating $templating;

    public function __construct(Router $router, Templating $templating)
    {
        $this->router = $router;
        $this->templating = $templating;
    }

    public function scrapeAction(): string
    {
        // Simulated scraping logic
        $data = [
            ["title" => "News 1", "content" => "Some scraped content..."],
            ["title" => "News 2", "content" => "Another scraped content..."]
        ];

        return $this->templating->render("scraper/index.html.php", [
            'data' => $data,
            'router' => $this->router,
        ]);
    }
}
