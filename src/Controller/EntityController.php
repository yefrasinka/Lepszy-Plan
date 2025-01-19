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
use App\Exception\NotFoundException;

class EntityController
{
    private Router $router;
    private Templating $templating;

    public function __construct(Router $router, Templating $templating)
    {
        $this->router = $router;
        $this->templating = $templating;
    }

    public function showEntityAction(string $entity, int $id): string
    {
        $modelClass = '\\App\\Model\\' . ucfirst($entity);

        if (!class_exists($modelClass)) {
            throw new NotFoundException("Entity $entity not found.");
        }

        $object = $modelClass::find($id);
        if (!$object) {
            throw new NotFoundException("$entity with ID $id not found.");
        }

        return $this->templating->render("entity/show.html.php", [
            'entity' => $object,
            'router' => $this->router,
        ]);
    }
}
