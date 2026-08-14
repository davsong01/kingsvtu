<?php

namespace App\Http\Controllers;

use App\Notifications\AnnouncementNotification;
use Illuminate\Http\Request;

class CustomerAnnouncementController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->where('type', AnnouncementNotification::class)
            ->latest()
            ->paginate(paginationRecords())
            ->withQueryString();

        $unreadCount = auth()->user()
            ->unreadNotifications()
            ->where('type', AnnouncementNotification::class)
            ->count();

        return view(themeView('customer', 'notifications'), compact('notifications', 'unreadCount'));
    }

    public function markAllAsRead()
    {
        auth()->user()
            ->unreadNotifications()
            ->where('type', AnnouncementNotification::class)
            ->get()
            ->each
            ->markAsRead();

        return back()->with('message', 'Notifications marked as read');
    }

    public function markAsRead($notification)
    {
        $item = auth()->user()
            ->notifications()
            ->where('type', AnnouncementNotification::class)
            ->whereKey($notification)
            ->first();

        if ($item) {
            $item->markAsRead();
        }

        return back();
    }
}
