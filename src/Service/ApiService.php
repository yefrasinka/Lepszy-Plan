<?php

namespace App\Service;

use App\ORM\EntityManager;

class ApiService
{
    private EntityManager $entityManager;
    private ScraperService $scraperService;

    public function __construct(
        EntityManager $entityManager,
        ScraperService $scraperService
    ) {
        $this->entityManager = $entityManager;
        $this->scraperService = $scraperService;
    }

    public function getScheduleByStudent(
        int $studentId,
        ?string $start = null,
        ?string $end = null,
        ?string $kind = null,
        ?string $query = null,
        ?string $teacher = null,
        ?string $room = null,
        ?string $group = null
    ): array {
        $user = $this->entityManager
            ->getRepository("App\Entity\User")
            ->findOneBy(["nrAlbumu" => $studentId]);

        if (!$user) {
            return [];
        }
        $qb = $this->entityManager->getRepository("App\Entity\Schedule")->createQueryBuilder();
        $qb->select('*')
            ->from("App\Entity\Schedule", "s")
            ->where("s.user_id = :user_id")
            ->setParameter("user_id", $user->getUserld())
            ->orderBy("s.startTime", "ASC");
        if ($teacher) {
            $qb->join("App\Entity\Worker", 'w','s.worker_id = w.id')
                ->andWhere("w.name = :teacher")
                ->setParameter("teacher", $teacher);
        }
        if ($room) {
            $qb->join("App\Entity\Room", 'r','s.room_id = r.id')
                ->andWhere("r.name = :room")
                ->setParameter("room", $room);
        }
        if ($group) {
            $qb->join("App\Entity\Group",'g','s.group_id = g.id')
                ->andWhere("g.name = :group")
                ->setParameter("group", $group);
        }

        if ($start && $end) {
            try {
                // Normalize timezones
                $start = $this->normalizeTimezone($start);
                $end = $this->normalizeTimezone($end);

                // Validate the format of the dates
                $startDate = \DateTime::createFromFormat(
                    "Y-m-d\TH:i:sO",
                    $start
                );
                $endDate = \DateTime::createFromFormat("Y-m-d\TH:i:sO", $end);

                if (!$startDate || !$endDate) {
                    throw new \Exception(
                        "Invalid date format. Ensure the dates are in the correct ISO 8601 format."
                    );
                }

                $qb->andWhere("s.startTime >= :start")
                    ->andWhere("s.startTime <= :end")
                    ->setParameter("start", $startDate)
                    ->setParameter("end", $endDate);
            } catch (\Exception $e) {
                return ["error" => "Date parsing error: " . $e->getMessage()];
            }
        }

        if ($kind === "subject" && $query) {
            $qb->join("App\Entity\Subject", "subj",'s.subject_id = subj.id')
                ->andWhere("subj.title = :query")
                ->setParameter("query", $query);
        }

        $schedules = $qb->getQuery();
        $result = [];
        $lessonTypeColors = [
            'laboratorium' => '#1A8238',
            'wykład' => '#247C84',
            'projekt' => '#555500',
            'egzamin' => '#007BB0',
            'audytoryjne' => '#007BB0',
            'rektorskie' => '#1A8238',
            'lektorat' => '#C44F00'
        ];
        foreach ($schedules as $schedule) {
            $date = $schedule->getStartTime()->format("Y-m-d");
            if (!isset($result[$date])) {
                $result[$date] = [];
            }
            $formattedSchedule = $this->formatScheduleData($schedule);

            $lessonForm = strtolower($formattedSchedule['lesson_form'] ?? '');

            $color = $lessonTypeColors[$lessonForm] ?? '#3788d8'; // default color if lesson type is not found
            $formattedSchedule['color'] = $color;


            $result[$date][] = $formattedSchedule;
        }

        return $result;
    }
    private function normalizeTimezone(string $date): string
    {
        // Check if the timezone format is already in "+HHMM" format
        if (preg_match('/[+-]\d{4}$/', $date)) {
            return $date; // Already normalized
        }
        // Replace "+02:00" or similar with "+0200"
        return preg_replace('/([+-]\d{2}):(\d{2})$/', '$1$2', $date);
    }

    private function validateISO8601Date(string $date): bool
    {
        $parsedDate = \DateTime::createFromFormat("Y-m-d\TH:i:sO", $date);
        return $parsedDate !== false;
    }
    private function formatScheduleData($schedule): array
    {
        $worker = null;
        $room = null;
        $group = null;
        $subject = null;

        if($schedule->getWorkerId()){
            $worker = $this->entityManager->getRepository('App\Entity\Worker')->find($schedule->getWorkerId());
            if($worker){
                $worker = [
                    "id" => $worker->getId(),
                    "name" => $worker->getName(),
                ];
            }
        }

        if($schedule->getRoomId()){
            $room = $this->entityManager->getRepository('App\Entity\Room')->find($schedule->getRoomId());
            if($room){
                $room = [
                    "id" => $room->getId(),
                    "name" => $room->getName(),
                ];
            }
        }
        if($schedule->getGroupId()){
            $group = $this->entityManager->getRepository('App\Entity\Group')->find($schedule->getGroupId());
            if($group){
                $group = [
                    "id" => $group->getId(),
                    "name" => $group->getName(),
                ];
            }

        }
        if($schedule->getSubjectId()){
            $subject = $this->entityManager->getRepository('App\Entity\Subject')->find($schedule->getSubjectId());
            if($subject){
                $subject =  [
                    "id" => $subject->getId(),
                    "title" => $subject->getTitle(),
                ];
            }

        }

        return [
            "id" => $schedule->getId(),
            "title" => $schedule->getTitle(),
            "description" => $schedule->getDescription(),
            "startTime" => $schedule->getStartTime()->format("Y-m-d H:i:s"),
            "endTime" => $schedule->getEndTime()->format("Y-m-d H:i:s"),
            "lesson_form"=>$schedule->getLessonForm(),
            "worker" => $worker,
            "room" => $room,
            "group" => $group,
            "subject" => $subject,
        ];
    }
}