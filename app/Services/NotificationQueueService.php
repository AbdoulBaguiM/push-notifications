<?php

namespace App\Services;

use PDO;
use PDOException;
use RuntimeException;

class NotificationQueueService
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addToQueueBulk(array $notificationData)
    {
        try {
            $placeholders = implode(',', array_fill(0, count($notificationData), '(?, ?)'));

            $insertQueue = $this->pdo->prepare("
                INSERT INTO notifications_queue (notification_id, device_token)
                VALUES {$placeholders}
            ");

            $flattenedData = [];
            foreach ($notificationData as $data) {
                $flattenedData[] = $data[':notification_id'];
                $flattenedData[] = $data[':device_token'];
            }

            $insertQueue->execute($flattenedData);
        } catch (PDOException $e) {
            throw new RuntimeException("Error adding to notification queue: {$e->getMessage()}");
        }
    }
}