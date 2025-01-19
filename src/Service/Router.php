<?php

namespace App\Service;

class Router
{
    public function generatePath(string $action, ?array $params = []): string
    {
        $query = $action ? http_build_query(array_merge(['action' => $action], $params)) : null;
        $path = "/index.php" . ($query ? "?$query" : "");
        return $path;
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
        // Include and execute generate_ical.php logic
        require __DIR__ . '/../generate_ical.php';
    }

    private function serveSeed(): void
    {
        // Include and execute Seeder logic
        require __DIR__ . '/../Service/Seeder.php';
        $seeder = new Seeder();
        $seeder->seed();
        echo "Database seeding complete!";
    }
}


