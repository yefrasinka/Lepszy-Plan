<?php

namespace App\Controller;

class LinkController
{
    public function generateShareableLink(array $filters): void
    {
        $queryString = http_build_query($filters);
        $link = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/index.php?' . $queryString;

        echo json_encode(['link' => $link]);
    }
}
