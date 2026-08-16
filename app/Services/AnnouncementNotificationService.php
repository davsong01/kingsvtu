<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Illuminate\Support\Facades\Notification;

class AnnouncementNotificationService
{
    public function publish(Announcement $announcement): int
    {
        if (($announcement->status ?? 'inactive') !== 'active') {
            return 0;
        }

        $customers = User::query()
            ->where('type', 'customer')
            ->get();

        if ($customers->isEmpty()) {
            return 0;
        }

        Notification::send($customers, new AnnouncementNotification($announcement));

        return $customers->count();
    }

    public function backfillForUser(User $user): int
    {
        if ($user->type !== 'customer') {
            return 0;
        }

        $activeAnnouncements = Announcement::query()
            ->where('status', 'active')
            ->latest()
            ->get();

        $published = 0;

        foreach ($activeAnnouncements as $announcement) {
            $alreadyNotified = $user->notifications()
                ->where('type', AnnouncementNotification::class)
                ->where('data->announcement_id', $announcement->id)
                ->exists();

            if ($alreadyNotified) {
                continue;
            }

            $user->notify(new AnnouncementNotification($announcement));
            $published++;
        }

        return $published;
    }
}
