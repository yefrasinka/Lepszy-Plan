<?php

namespace App\Controller;

use App\Controller\ScheduleController;
use App\Controller\ApiController;

class UserScheduleController {
    private $scheduleController;
    private $apiController;

    public function __construct() {
        $this->scheduleController = new ScheduleController();
        $this->apiController = new ApiController();
    }

    public function getStudentSchedule($filters, $studentId) {
        $filters['album'] = $studentId;
        $schedules = $this->scheduleController->filterSchedules($filters);

        if (empty($schedules)) {
            $params = [
                'number' => $studentId,
                'start' => $filters['start'] ?? date('Y-m-d'),
                'end' => $filters['end'] ?? date('Y-m-d', strtotime('+7 days')),
            ];
            $schedules = $this->apiController->fetchFromZUT($params);
        }

        return $schedules;
    }
}
