<?php

namespace App\Service;

class Router
{
    public function generatePath(string $action, ?array $params = []): string
    {
        $query = $action ? http_build_query(array_merge(['action' => $action], $params)) : null;
        return "/index.php" . ($query ? "?$query" : "");
    }

    public function redirect($path): void
    {
        header("Location: $path");
        exit();
    }

    public function handleRequest(): void
    {
        $action = $_GET['action'] ?? null;

        switch ($action) {
            case 'generate_ical':
                $this->serveGenerateIcal();
                break;
            case 'filter_schedules':
                $this->serveFilterSchedules();
                break;
            case 'student_schedule':
                $this->serveStudentSchedule();
                break;
            case 'save_favorite':
                $this->serveSaveFavorite();
                break;
            case 'get_favorites':
                $this->serveGetFavorites();
                break;
            case 'seed':
                $this->serveSeed();
                break;
            default:
                echo "Invalid action";
                http_response_code(404);
                break;
        }
    }

    private function serveGenerateIcal(): void
    {
        require __DIR__ . '/../Controller/ICalendarController.php';
        $controller = new \App\Controller\ICalendarController();
        $filters = $_GET;
        $scheduleController = new \App\Controller\ScheduleController();
        $schedules = $scheduleController->filterSchedules($filters);
        $controller->exportToICalendar($schedules);
    }

    private function serveFilterSchedules(): void
    {
        require __DIR__ . '/../Controller/ScheduleController.php';
        $controller = new \App\Controller\ScheduleController();
        $filters = $_GET;
        $results = $controller->filterSchedules($filters);
        header('Content-Type: application/json');
        echo json_encode($results);
    }

    private function serveStudentSchedule(): void
    {
        require __DIR__ . '/../Controller/UserScheduleController.php';
        $controller = new \App\Controller\UserScheduleController();
        $filters = $_GET;
        $studentId = $_GET['album'] ?? null;
        $schedules = $controller->getStudentSchedule($filters, $studentId);
        header('Content-Type: application/json');
        echo json_encode($schedules);
    }

    private function serveSaveFavorite(): void
    {
        require __DIR__ . '/../Controller/FavoriteController.php';
        $controller = new \App\Controller\FavoriteController();
        $scheduleId = $_GET['schedule_id'] ?? null;
        $controller->saveToFavorites($scheduleId);
        echo json_encode(['status' => 'success']);
    }

    private function serveGetFavorites(): void
    {
        require __DIR__ . '/../Controller/FavoriteController.php';
        $controller = new \App\Controller\FavoriteController();
        $favorites = $controller->getFavorites();
        header('Content-Type: application/json');
        echo json_encode($favorites);
    }

    private function serveSeed(): void
    {
        require __DIR__ . '/../Service/Seeder.php';
        $seeder = new \App\Service\Seeder();
        $seeder->seed();
        echo "Database seeding complete!";
    }
}
