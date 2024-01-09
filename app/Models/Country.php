<?php

namespace App\Models;

use PDO;
use PDOException;
use RuntimeException;

class Country
{
    public static function exists(int $countryId, PDO $pdo): bool
    {
        try {
            $query = $pdo->prepare("SELECT EXISTS(SELECT 1 FROM countries WHERE id = :country_id)");
            $query->execute([':country_id' => $countryId]);

            return (bool)($query->fetchColumn());

        } catch (PDOException $e) {
            throw new RuntimeException("Error checking country existence: {$e->getMessage()}");
        }
    }

    public static function usersAndDevices(int $countryId, PDO $pdo): array
    {
        try {
            $query = $pdo->prepare("
                SELECT u.id AS user_id, d.token
                FROM users u
                JOIN devices d ON u.id = d.user_id AND d.expired = FALSE
                WHERE u.country_id = :country_id
            ");

            $query->execute([':country_id' => $countryId]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException("Error fetching users and devices by country ID: {$e->getMessage()}");
        }
    }
}