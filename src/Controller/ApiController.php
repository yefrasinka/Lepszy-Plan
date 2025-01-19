<?php
namespace App\Controller;

use App\Service\Router;
use App\Service\Templating;
use App\Model\Worker;
use App\Model\User;
use App\Model\Schedule;
use App\Model\Group;
use App\Model\Room;
use App\Model\Subject;

class ApiController
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
        return json_encode([
            'message' => 'Welcome to the API',
            'routes' => [
                'GET /workers',
                'GET /users',
                'GET /schedules',
                'GET /groups',
                'GET /rooms',
                'GET /subjects'
            ]
        ]);
    }

    public function getWorkersAction(): string
    {
        return json_encode(Worker::findAll());
    }

    public function getUsersAction(): string
    {
        return json_encode(User::findAll());
    }

    public function getSchedulesAction(): string
    {
        return json_encode(Schedule::findAll());
    }

    public function getGroupsAction(): string
    {
        return json_encode(Group::findAll());
    }

    public function getRoomsAction(): string
    {
        return json_encode(Room::findAll());
    }

    public function getSubjectsAction(): string
    {
        return json_encode(Subject::findAll());
    }
}
