<?php

namespace App\Controller;

class FavouriteController
{
    public function saveFavorite(int $scheduleId): void
    {
        $favorites = $_COOKIE['favorites'] ?? '[]';
        $favorites = json_decode($favorites, true);

        if (!in_array($scheduleId, $favorites)) {
            $favorites[] = $scheduleId;
        }

        setcookie('favorites', json_encode($favorites), time() + (86400 * 30), "/");
        echo json_encode(['success' => true, 'message' => 'Added to favorites.']);
    }

    public function getFavorites(): void
    {
        $favorites = $_COOKIE['favorites'] ?? '[]';
        echo $favorites;
    }
}
