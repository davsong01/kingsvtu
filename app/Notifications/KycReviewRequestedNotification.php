<?php

namespace App\Notifications;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class KycReviewRequestedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Customer $customer)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'KYC review requested',
            'message' => trim(($this->customer->user->name ?? $this->customer->user->firstname ?? 'Customer') . ' has completed KYC and requested admin review.'),
            'customer_id' => $this->customer->id,
            'customer_name' => $this->customer->user->name ?? $this->customer->user->firstname ?? 'Customer',
            'customer_email' => $this->customer->user->email ?? null,
            'customer_phone' => $this->customer->user->phone ?? null,
            'requested_at' => now()->toIso8601String(),
        ];
    }
}
