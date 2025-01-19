<?php
namespace App\Controller;

use App\Service\Router;
use App\Service\Templating;
use App\Model\Schedule;
use App\Model\User;
use App\Exception\NotFoundException;

class UserScheduleController
{
    private Router $router;
    private Templating $templating;

    public function __construct(Router $router, Templating $templating)
    {
        $this->router = $router;
        $this->templating = $templating;
    }

    public function showUserScheduleAction(int $userId): string
    {
        $user = User::find($userId);
        if (!$user) {
            throw new NotFoundException("User with ID $userId not found.");
        }

        $schedules = Schedule::findAll(); // Adjust as needed to filter by user
        return $this->templating->render("schedule/user_schedule.html.php", [
            'user' => $user,
            'schedules' => $schedules,
            'router' => $this->router,
        ]);
    }
}
