<?php

namespace App\Service;

use PDO;

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $databaseFile = __DIR__ . '/../../data.db';

        $this->pdo = new PDO('sqlite:' . $databaseFile);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->initializeTables();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    private function initializeTables() {
        $tables = [
            "CREATE TABLE IF NOT EXISTS Groups (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT UNIQUE NOT NULL
        );",
            "CREATE TABLE IF NOT EXISTS Rooms (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT UNIQUE NOT NULL
        );",
            "CREATE TABLE IF NOT EXISTS Subjects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT UNIQUE NOT NULL,
            color TEXT,
            border_color TEXT
        );",
            "CREATE TABLE IF NOT EXISTS Workers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name TEXT UNIQUE NOT NULL,
            title TEXT
        );",
            "CREATE TABLE IF NOT EXISTS Users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            student_id INTEGER UNIQUE NOT NULL
        );",
            "CREATE TABLE IF NOT EXISTS Schedules (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            start_time TEXT NOT NULL,
            end_time TEXT NOT NULL,
            lesson_form TEXT,
            lesson_form_short TEXT,
            lesson_status TEXT,
            lesson_status_short TEXT,
            status_item TEXT,
            hours REAL,
            worker_id INTEGER,
            room_id INTEGER,
            group_id INTEGER,
            subject_id INTEGER,
            FOREIGN KEY (worker_id) REFERENCES Workers (id),
            FOREIGN KEY (room_id) REFERENCES Rooms (id),
            FOREIGN KEY (group_id) REFERENCES Groups (id),
            FOREIGN KEY (subject_id) REFERENCES Subjects (id)
        );",
            // New join table for many-to-many relationship
            "CREATE TABLE IF NOT EXISTS Schedule_Users (
            schedule_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            PRIMARY KEY (schedule_id, user_id),
            FOREIGN KEY (schedule_id) REFERENCES Schedules (id),
            FOREIGN KEY (user_id) REFERENCES Users (id)
        );"
        ];

        foreach ($tables as $table) {
            try {
                $this->pdo->exec($table);
            } catch (\PDOException $e) {
                error_log("Table creation error: " . $e->getMessage());
            }
        }
    }
}
