<?php
namespace App\Controller;

use App\Service\Router;
use App\Service\Templating;
use App\Model\Schedule;
use App\Exception\NotFoundException;

class ExternalScheduleController
{
    private Router $router;
    private Templating $templating;

    public function __construct(Router $router, Templating $templating)
    {
        $this->router = $router;
        $this->templating = $templating;
    }

    public function fetchExternalScheduleAction(): string
    {
        // Simulating an external schedule API call
        $externalData = [
            ["id" => 1, "subject" => "Math", "time" => "08:00 AM"],
            ["id" => 2, "subject" => "Physics", "time" => "10:00 AM"]
        ];

        return json_encode($externalData);
    }

    public function syncExternalScheduleAction(): void
    {
        $externalData = json_decode($this->fetchExternalScheduleAction(), true);

        foreach ($externalData as $entry) {
            $schedule = new Schedule();
            $schedule->setSubjectId($entry['id']);
            $schedule->setTimeSlot($entry['time']);
            $schedule->save();
        }

        $this->router->redirect($this->router->generatePath('schedule-index'));
    }
}