<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use RuntimeException;

readonly class EmailNotificationService implements NotificationServiceInterface
{
    public function __construct(private string $emailTo) {}

    public function notify(string $message): void
    {
        $to = $this->emailTo;
        $subject = 'the subject';
        $headers = 'From: webmaster@example.com'       . "\r\n" .
            'Reply-To: webmaster@example.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        if (false === mail($to, $subject, $message, $headers)) {
            throw new RuntimeException('Trouble with send mail');
        }
    }
}
