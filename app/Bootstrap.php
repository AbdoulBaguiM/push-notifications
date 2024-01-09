<?php

namespace App;

use App\Controllers\Controller;
use App\Controllers\PushNotificationController;
use JsonException;

class Bootstrap
{
    public array $actions = [
        'send',
        'details',
        'cron',
    ];

    private string $action;
    private Controller $controller;

    public function __construct(string $action)
    {
        if (!in_array($action, $this->actions)) {
            forbidden();
        }

        $this->action = $action;
        $this->controller = new PushNotificationController();

        $this->run();
    }

    private function run(): void
    {
        switch ($this->action) {
            case 'send':
                $title = $_POST['title'] ?? null;
                $message = $_POST['message'] ?? null;
                $countryId = $_POST['country_id'] ?? null;

                if (!$title || !$message || !$countryId) {
                    forbidden();
                }

                $result = $this->controller->sendByCountryId($title, $message, (int)$countryId);
                break;

            case 'details':
                $notificationId = $_POST['notification_id'] ?? null;

                if (!$notificationId) {
                    forbidden();
                }

                $result = $this->controller->details($notificationId);
                break;

            case 'cron':
                $result = $this->controller->cron();
                break;
            default:
                $result = null;
        }

        try {
            response($result !== null, $result);
        } catch (JsonException $e) {
            forbidden();
        }
    }
}