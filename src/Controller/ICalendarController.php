<?php

namespace App\Controller;

use App\Service\Database;

class ICalendarController
{
    public function exportToICalendar(array $filters = []): void
    {
        try {
            $pdo = Database::getInstance()->getConnection();
        } catch (\PDOException $e) {
            http_response_code(500);
            echo "Database connection failed: " . $e->getMessage();
            return;
        }

        $query = "
            SELECT 
                subjects.name AS subject_name, 
                rooms.name AS location, 
                schedules.start_time, 
                schedules.end_time 
            FROM schedules
            JOIN subjects ON schedules.subject_id = subjects.id
            JOIN rooms ON schedules.room_id = rooms.id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filters['wykladowca'])) {
            $query .= " AND schedules.worker_id = (SELECT id FROM workers WHERE full_name LIKE :wykladowca)";
            $params[':wykladowca'] = '%' . $filters['wykladowca'] . '%';
        }
        if (!empty($filters['sala'])) {
            $query .= " AND rooms.name LIKE :sala";
            $params[':sala'] = '%' . $filters['sala'] . '%';
        }
        if (!empty($filters['grupa'])) {
            $query .= " AND schedules.group_id = (SELECT id FROM groups WHERE name LIKE :grupa)";
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

        if (!empty($filters['start']) && !empty($filters['end'])) {
            $query .= " AND schedules.start_time >= :start AND schedules.end_time <= :end";
            $params[':start'] = $filters['start'];
            $params[':end'] = $filters['end'];
        }

        $statement = $pdo->prepare($query);
        $statement->execute($params);

        $events = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $ical = $this->buildICalendar($events);

        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="schedule.ics"');
        echo $ical;
    }

    private function buildICalendar(array $events): string
    {
        $ical = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//Your Application//NONSGML v1.0//EN\r\nCALSCALE:GREGORIAN\r\nMETHOD:PUBLISH\r\n";

        foreach ($events as $event) {
            $startTime = date('Ymd\THis\Z', strtotime($event['start_time']));
            $endTime = date('Ymd\THis\Z', strtotime($event['end_time']));
            $summary = htmlspecialchars($event['subject_name'], ENT_QUOTES);
            $location = htmlspecialchars($event['location'], ENT_QUOTES);

            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:" . uniqid() . "@yourdomain.com\r\n";
            $ical .= "SUMMARY:$summary\r\n";
            $ical .= "DTSTART:$startTime\r\n";
            $ical .= "DTEND:$endTime\r\n";
            $ical .= "LOCATION:$location\r\n";
            $ical .= "DESCRIPTION:$summary\r\n";
            $ical .= "STATUS:CONFIRMED\r\n";
            $ical .= "TRANSP:OPAQUE\r\n";
            $ical .= "END:VEVENT\r\n";
        }

        $ical .= "END:VCALENDAR\r\n";
        return $ical;
    }
}
