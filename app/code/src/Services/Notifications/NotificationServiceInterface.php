<?php

declare(strict_types=1);

namespace App\Services\Notifications;

interface NotificationServiceInterface
{
    public function notify(string $message): void;
}
