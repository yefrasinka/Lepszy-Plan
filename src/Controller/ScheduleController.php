<?php
namespace App\Controller;

use App\Service\Router;
use App\Service\Templating;
use App\Model\Schedule;
use App\Exception\NotFoundException;

class ScheduleController
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
        $schedules = Schedule::findAll();
        return $this->templating->render("schedule/index.html.php", [
            'schedules' => $schedules,
            'router' => $this->router,
        ]);
    }

    public function showAction(int $id): string
    {
        $schedule = Schedule::find($id);
        if (!$schedule) {
            throw new NotFoundException("Schedule with ID $id not found.");
        }

        return $this->templating->render("schedule/show.html.php", [
            'schedule' => $schedule,
            'router' => $this->router,
        ]);
    }
}
