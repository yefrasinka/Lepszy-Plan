<?php

namespace App\Controller;

use App\Service\Seeder;

class ApiController {
    public function fetchFromZUT($params) {
        $url = "https://plan.zut.edu.pl/schedule_student.php?";
        $query = http_build_query($params);
        $url .= $query;

        $seeder = new Seeder();
        $data = $seeder->fetchData($url);

        if ($data) {
            $seeder->processData($data);
        }

        return $data;
    }
}
