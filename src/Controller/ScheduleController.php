<?php

namespace App\Controller;

use App\Service\Database;

class ScheduleController
{
    public function getSchedules(array $filters = []): void
    {
        try {
            $pdo = Database::getInstance()->getConnection();
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
            return;
        }

        $query = "
            SELECT 
                schedules.id,
                schedules.title,
                schedules.start_time,
                schedules.end_time,
                subjects.name AS subject,
                rooms.name AS room,
                workers.full_name AS worker,
                groups.name AS group_name
            FROM schedules
            LEFT JOIN subjects ON schedules.subject_id = subjects.id
            LEFT JOIN rooms ON schedules.room_id = rooms.id
            LEFT JOIN workers ON schedules.worker_id = workers.id
            LEFT JOIN groups ON schedules.group_id = groups.id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filters['wykladowca'])) {
            $query .= " AND workers.full_name LIKE :wykladowca";
            $params[':wykladowca'] = '%' . $filters['wykladowca'] . '%';
        }
        if (!empty($filters['sala'])) {
            $query .= " AND rooms.name LIKE :sala";
            $params[':sala'] = '%' . $filters['sala'] . '%';
        }
        if (!empty($filters['grupa'])) {
            $query .= " AND groups.name LIKE :grupa";
            $params[':grupa'] = '%' . $filters['grupa'] . '%';
        }
        if (!empty($filters['album'])) {
            $query .= " AND schedules.group_id IN (
                SELECT group_id FROM student_groups WHERE student_id = (SELECT id FROM students WHERE album_number = :album)
            )";
            $params[':album'] = $filters['album'];
        }
        if (!empty($filters['przedmiot'])) {
            $query .= " AND subjects.name LIKE :przedmiot";
            $params[':przedmiot'] = '%' . $filters['przedmiot'] . '%';
        }

        $statement = $pdo->prepare($query);
        $statement->execute($params);

        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($results);
    }
}
