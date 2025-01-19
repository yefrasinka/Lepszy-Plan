<?php

namespace App\Service;

require_once 'Database.php';
use App\Service\Database;

class Seeder {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function seed() {
        // Student IDs for users
        $studentIds = [53021, 53869]; // Add more IDs as needed
        $this->seedUsers($studentIds);

        // URLs to fetch schedules for students
        $urls = [
            [
                'student_id' => 53021,
                'url' => "https://plan.zut.edu.pl/schedule_student.php?number=53021&start=2025-01-13T00%3A00%3A00%2B01%3A00&end=2025-01-20T00%3A00%3A00%2B01%3A00"
            ],
            [
                'student_id' => 53869,
                'url' => "https://plan.zut.edu.pl/schedule_student.php?number=53869&start=2025-01-13T00%3A00%3A00%2B01%3A00&end=2025-01-20T00%3A00%3A00%2B01%3A00"
            ]
        ];

        foreach ($urls as $urlInfo) {
            $data = $this->fetchData($urlInfo['url']);
            if ($data) {
                $this->processData($data, $urlInfo['student_id']);
            }
        }

        echo "Database seeding complete!\n";
    }

    private function fetchData($url) {
        try {
            $response = file_get_contents($url);
            if ($response === false) {
                throw new \Exception("Failed to fetch data from API: $url");
            }

            $data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON response from API: $url");
            }

            return $data;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    private function processData($data, $studentId) {
        $rooms = array_unique(array_filter(array_column($data, 'room')));
        $groups = array_unique(array_filter(array_column($data, 'group_name')));
        $subjects = array_unique(array_filter(array_column($data, 'subject')));

        // Handle workers safely by filtering out invalid entries
        $workers = array_map(function ($entry) {
            return [
                'full_name' => $entry['worker'] ?? null,
                'title' => $entry['worker_title'] ?? null
            ];
        }, $data);
        $workers = array_filter($workers, function ($worker) {
            return !empty($worker['full_name']);
        });

        // Seed dependent tables first
        $this->seedRooms($rooms);
        $this->seedGroups($groups);
        $this->seedSubjects($subjects);
        $this->seedWorkers($workers);

        // Seed schedules and associate the student with them
        $this->seedSchedules($data, $studentId);
    }

    private function seedUsers($studentIds) {
        foreach ($studentIds as $studentId) {
            $stmt = $this->db->prepare("INSERT OR IGNORE INTO Users (student_id) VALUES (:student_id)");
            try {
                $stmt->execute([':student_id' => $studentId]);
                echo "Inserted user: $studentId\n";
            } catch (\PDOException $e) {
                error_log("Error inserting user: " . $e->getMessage());
            }
        }
    }

    private function seedGroups($groups) {
        foreach ($groups as $group) {
            if (empty($group)) continue;

            $stmt = $this->db->prepare("INSERT OR IGNORE INTO Groups (name) VALUES (:name)");
            try {
                $stmt->execute([':name' => $group]);
                echo "Inserted group: $group\n";
            } catch (\PDOException $e) {
                error_log("Error inserting group: " . $e->getMessage());
            }
        }
    }

    private function seedRooms($rooms) {
        foreach ($rooms as $room) {
            if (empty($room)) continue;

            $stmt = $this->db->prepare("INSERT OR IGNORE INTO Rooms (name) VALUES (:name)");
            try {
                $stmt->execute([':name' => $room]);
                echo "Inserted room: $room\n";
            } catch (\PDOException $e) {
                error_log("Error inserting room: " . $e->getMessage());
            }
        }
    }

    private function seedSubjects($subjects) {
        foreach ($subjects as $subject) {
            if (empty($subject)) continue;

            $stmt = $this->db->prepare("INSERT OR IGNORE INTO Subjects (name, color, border_color) VALUES (:name, :color, :border_color)");
            try {
                $stmt->execute([
                    ':name' => $subject,
                    ':color' => null,
                    ':border_color' => null
                ]);
                echo "Inserted subject: $subject\n";
            } catch (\PDOException $e) {
                error_log("Error inserting subject: " . $e->getMessage());
            }
        }
    }

    private function seedWorkers($workers) {
        foreach ($workers as $worker) {
            if (empty($worker['full_name'])) continue;

            $stmt = $this->db->prepare("INSERT OR IGNORE INTO Workers (full_name, title) VALUES (:full_name, :title)");
            try {
                $stmt->execute([
                    ':full_name' => $worker['full_name'],
                    ':title' => $worker['title'] ?? null
                ]);
                echo "Inserted worker: {$worker['full_name']}\n";
            } catch (\PDOException $e) {
                error_log("Error inserting worker: " . $e->getMessage());
            }
        }
    }

    private function seedSchedules($schedules, $studentId) {
        foreach ($schedules as $schedule) {
            if (empty($schedule) || !isset($schedule['title'], $schedule['start'], $schedule['end'])) continue;

            $stmt = $this->db->prepare("
                INSERT OR IGNORE INTO Schedules 
                (title, description, start_time, end_time, lesson_form, lesson_form_short, lesson_status, lesson_status_short, status_item, hours, room_id, group_id, subject_id, worker_id)
                VALUES 
                (:title, :description, :start_time, :end_time, :lesson_form, :lesson_form_short, :lesson_status, :lesson_status_short, :status_item, :hours,
                (SELECT id FROM Rooms WHERE name = :room),
                (SELECT id FROM Groups WHERE name = :group),
                (SELECT id FROM Subjects WHERE name = :subject),
                (SELECT id FROM Workers WHERE full_name = :worker)
                )
            ");

            try {
                $stmt->execute([
                    ':title' => $schedule['title'],
                    ':description' => $schedule['description'] ?? '',
                    ':start_time' => $schedule['start'],
                    ':end_time' => $schedule['end'],
                    ':lesson_form' => $schedule['lesson_form'] ?? '',
                    ':lesson_form_short' => $schedule['lesson_form_short'] ?? '',
                    ':lesson_status' => $schedule['lesson_status'] ?? '',
                    ':lesson_status_short' => $schedule['lesson_status_short'] ?? '',
                    ':status_item' => $schedule['status_item'] ?? '',
                    ':hours' => $schedule['hours'] ?? 0,
                    ':room' => $schedule['room'] ?? '',
                    ':group' => $schedule['group_name'] ?? '',
                    ':subject' => $schedule['subject'] ?? '',
                    ':worker' => $schedule['worker'] ?? ''
                ]);

                $scheduleId = $this->db->lastInsertId();
                $this->associateStudentWithSchedule($scheduleId, $studentId);

                echo "Inserted schedule: {$schedule['title']} ({$schedule['start']} - {$schedule['end']})\n";
            } catch (\PDOException $e) {
                error_log("Error inserting schedule: " . $e->getMessage());
            }
        }
    }

    private function associateStudentWithSchedule($scheduleId, $studentId) {
        $stmt = $this->db->prepare("
            INSERT OR IGNORE INTO Schedule_Users (schedule_id, user_id) 
            VALUES (
                :schedule_id,
                (SELECT id FROM Users WHERE student_id = :student_id)
            )
        ");
        try {
            $stmt->execute([
                ':schedule_id' => $scheduleId,
                ':student_id' => $studentId
            ]);
            echo "Associated student $studentId with schedule $scheduleId\n";
        } catch (\PDOException $e) {
            error_log("Error associating student with schedule: " . $e->getMessage());
        }
    }
}
