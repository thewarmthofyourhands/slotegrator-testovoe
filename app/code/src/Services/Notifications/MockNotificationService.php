<?php

declare(strict_types=1);

namespace App\Services\Notifications;

readonly class MockNotificationService implements NotificationServiceInterface
{
    public function notify(string $message): void
    {
        file_put_contents('var/notifications/messages.txt', $message);
    }
}
