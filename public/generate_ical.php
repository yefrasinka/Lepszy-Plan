<?php

require_once __DIR__ . '/autoload.php';
use App\Service\Config;
class ICalGenerator
{
    public static function generate(): void
    {
        try {
            $pdo = new PDO(Config::get('db_dsn'));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }

        $statement = $pdo->query(
            "SELECT subjects.name, rooms.name AS location, schedules.time_slot AS start_time, schedules.time_slot AS end_time 
            FROM schedules 
            JOIN subjects ON schedules.subject_id = subjects.id 
            JOIN rooms ON schedules.room_id = rooms.id 
            LIMIT 5"
        );

        $events = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Start iCalendar format
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Your Company//NONSGML v1.0//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";

        if ($events) {
            foreach ($events as $event) {
                $startTime = date('Ymd\THis\Z', strtotime($event['start_time']));
                $endTime = date('Ymd\THis\Z', strtotime($event['end_time']));
                $summary = htmlspecialchars($event['name'], ENT_QUOTES);
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
        } else {
            // Add a placeholder event if no records are found
            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:" . uniqid() . "@yourdomain.com\r\n";
            $ical .= "SUMMARY:No Events Found\r\n";
            $ical .= "DTSTART:" . date('Ymd\THis\Z') . "\r\n";
            $ical .= "DTEND:" . date('Ymd\THis\Z', strtotime('+1 hour')) . "\r\n";
            $ical .= "DESCRIPTION:No events available in the schedule.\r\n";
            $ical .= "STATUS:TENTATIVE\r\n";
            $ical .= "TRANSP:OPAQUE\r\n";
            $ical .= "END:VEVENT\r\n";
        }

        $ical .= "END:VCALENDAR\r\n";

        // Set headers for downloading the .ics file
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="schedule.ics"');
        echo $ical;
    }
}

// Execute the generator
ICalGenerator::generate();


// Execute the generator
ICalGenerator::generate();
