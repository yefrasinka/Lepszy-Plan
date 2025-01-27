<?php

namespace App\Controller;

use App\Utils\JsonResponse;
use App\Utils\Request;
class ApiController
{

    public function getSuggestions(Request $request, string $kind): JsonResponse
    {
        $query = $request->query['query'] ?? null;
        if (!$query) {
            return new JsonResponse([]);
        }
        try {
            $url =
                "https://plan.zut.edu.pl/schedule.php?kind={$kind}&query=" .
                urlencode($query);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode !== 200) {
                throw new \Exception("HTTP Error: $httpCode");
            }
            $data = json_decode($response, true);

            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON response from plan.zut.edu.pl");
            }
            curl_close($ch);
            return new JsonResponse($data);
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    "error" =>
                        "Failed to fetch suggestions from plan.zut.edu.pl: " .
                        $e->getMessage(),
                ],
                500
            );
        }
    }
}