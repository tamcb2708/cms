<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminAccountLockedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly User $lockedUser, private readonly string $ipAddress)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Cảnh báo: Tài khoản quản trị bị khoá tạm thời')
            ->line("Tài khoản \"{$this->lockedUser->name}\" ({$this->lockedUser->email}) đã bị khoá tạm thời sau ".User::MAX_LOGIN_ATTEMPTS.' lần đăng nhập sai liên tiếp.')
            ->line("Địa chỉ IP: {$this->ipAddress}")
            ->line('Tài khoản sẽ tự động mở khoá sau '.User::LOCKOUT_MINUTES.' phút.');
    }
}
