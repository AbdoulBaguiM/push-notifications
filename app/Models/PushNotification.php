<?php


namespace App\Models;


use App\Services\DatabaseService;
use App\Services\NotificationQueueService;
use Exception;
use PDO;
use PDOException;
use RuntimeException;

class PushNotification
{
    private const STATUS_SENT = 'sent';
    private const STATUS_FAILED = 'failed';
    private const STATUS_QUEUED = 'queued';

    /**
     * @throws Exception
     */
    public static function send(string $title, string $message, string $token): bool
    {
        return random_int(1, 10) > 1;
    }

    public static function sendByCountryId(DatabaseService $databaseService, NotificationQueueService $notificationQueueService, string $title, string $message, int $countryId): ?int
    {
        $pdo = $databaseService->getPDO();

        if (!Country::exists($countryId, $pdo)) {
            return null;
        }

        try {
            $databaseService->beginTransaction();

            $notificationId = self::createNotification($pdo, $title, $message, $countryId);
            self::addToQueue($pdo, $notificationQueueService, $notificationId, $countryId);

            $databaseService->commit();

            return $notificationId;

        } catch (PDOException $e) {
            $databaseService->rollBack();
            throw new RuntimeException("Error sending notification: {$e->getMessage()}");
        }
    }

    private static function createNotification(PDO $pdo, string $title, string $message, int $countryId): int
    {
        $insertNotification = $pdo->prepare("
            INSERT INTO notifications (title, message, country_id)
            VALUES (:title, :message, :country_id)
        ");

        $insertNotification->execute([
            ':title' => $title,
            ':message' => $message,
            ':country_id' => $countryId,
        ]);

        return (int)$pdo->lastInsertId();
    }

    private static function addToQueue(PDO $pdo, NotificationQueueService $notificationQueueService, string $notificationId, int $countryId): void
    {
        $usersAndDevices = Country::usersAndDevices($countryId, $pdo);
        $notificationData = [];
        foreach ($usersAndDevices as $userAndDevice) {
            $notificationData[] = [
                ':notification_id' => $notificationId,
                ':device_token' => $userAndDevice['token'],
            ];
        }
        $notificationQueueService->addToQueueBulk($notificationData);
    }

    public static function getDetails(DatabaseService $databaseService, int $notificationId)
    {
        $pdo = $databaseService->getPDO();

        if (!self::exists($notificationId, $pdo)) {
            return null;
        }

        try {
            $query = $pdo->prepare("
                SELECT 
                    n.id, 
                    n.title, 
                    n.message, 
                    n.sent, 
                    n.failed,
                    COUNT(CASE WHEN q.status = 'queued' THEN q.notification_id END) as in_progress,
                    COUNT(q.notification_id) as in_queue
                FROM notifications n
                LEFT JOIN notifications_queue q ON n.id = q.notification_id
                WHERE n.id = :notification_id
                GROUP BY n.id
            ");
            $query->execute([':notification_id' => $notificationId]);

            return $query->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            throw new RuntimeException("Error fetching notification details: {$e->getMessage()}");
        }
    }

    public static function processNotificationsInQueue(DatabaseService $databaseService, int $limit): array
    {
        $pdo = $databaseService->getPDO();
        if (!self::hasAtLeastOneNotificationInQueue($pdo)) {
            return [];
        }

        $notificationsInQueue = self::fetchNotificationsInQueue($pdo, $limit);

        return self::processNotifications($pdo, $notificationsInQueue);
    }

    private static function fetchNotificationsInQueue(PDO $pdo, int $limit): array
    {
        $query = $pdo->prepare("
            SELECT nq.id as queue_id, n.id as notification_id, n.title, n.message, nq.device_token
            FROM notifications_queue nq
            JOIN notifications n ON nq.notification_id = n.id
            WHERE nq.status = :status
            LIMIT :limit
        ");
        $query->bindValue(':status', self::STATUS_QUEUED);
        $query->bindValue(':limit', $limit, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function processNotifications(PDO $pdo, array $notificationsInQueue): array
    {
        $results = [];
        $sentIds = [];
        $failedIds = [];

        foreach ($notificationsInQueue as $notification) {
            try {
                $isSent = PushNotification::send(
                    $notification['title'],
                    $notification['message'],
                    $notification['device_token']
                );

                if ($isSent) {
                    $sentIds[] = $notification['queue_id'];
                } else {
                    $failedIds[] = $notification['queue_id'];
                }

                $results = self::updateResults($results, $notification, $isSent);
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
        }

        self::updateDatabase($pdo, $sentIds, $failedIds, $results);

        return array_values($results);
    }

    private static function updateResults(array $results, array $notification, bool $isSent): array
    {
        $notificationId = $notification['notification_id'];

        if (!isset($results[$notificationId])) {
            $results[$notificationId] = [
                'notification_id' => $notificationId,
                'title' => $notification['title'],
                'message' => $notification['message'],
                'sent' => 0,
                'failed' => 0,
            ];
        }

        $results[$notificationId]['sent'] += $isSent ? 1 : 0;
        $results[$notificationId]['failed'] += $isSent ? 0 : 1;

        return $results;
    }

    private static function updateDatabase(PDO $pdo, array $sentIds, array $failedIds, array $results): void
    {
        $pdo->beginTransaction();

        self::updateQueueStatus($pdo, $sentIds, self::STATUS_SENT);
        self::updateQueueStatus($pdo, $failedIds, self::STATUS_FAILED);

        foreach ($results as $notificationId => $result) {
            self::updateNotificationStatistics($pdo, $notificationId, $result);
        }

        $pdo->commit();
    }

    private static function updateQueueStatus(PDO $pdo, array $ids, string $status): void
    {
        if (!empty($ids)) {
            $query = $pdo->prepare("
                UPDATE notifications_queue
                SET status = :status
                WHERE id IN (" . implode(',', $ids) . ")
            ");
            $query->bindValue(':status', $status);
            $query->execute();
        }
    }

    private static function updateNotificationStatistics(PDO $pdo, int $notificationId, array $result): void
    {
        $query = $pdo->prepare("
            UPDATE notifications
            SET sent = sent + :sent, failed = failed + :failed
            WHERE id = :notification_id
        ");
        $query->execute([
            ':sent' => $result['sent'],
            ':failed' => $result['failed'],
            ':notification_id' => $notificationId,
        ]);
    }

    private static function exists(int $notificationId, PDO $pdo): bool
    {
        $query = $pdo->prepare("SELECT EXISTS(SELECT 1 FROM notifications WHERE id = :notification_id)");
        $query->execute([':notification_id' => $notificationId]);

        return (bool)($query->fetchColumn());
    }

    public static function hasAtLeastOneNotificationInQueue(PDO $pdo): bool
    {
        $query = $pdo->query("SELECT 1 FROM notifications_queue WHERE status = 'queued' LIMIT 1");
        $result = $query->fetch(PDO::FETCH_COLUMN);

        return $result !== false;
    }
}