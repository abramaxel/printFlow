<?php

use App\Models\User;
use App\Notifications\PaymentPendingNotification;
use App\Notifications\StockCriticalNotification;
use App\Notifications\DeadlineApproachingNotification;

if (!function_exists('notify')) {
    /**
     * Kirim notifikasi ke user tertentu atau semua staff internal
     */
    function notify(string|array $recipients, \Illuminate\Notifications\Notification $notification): void
    {
        if ($recipients === 'internal') {
            // Kirim ke semua staff internal (owner, admin, cs, produksi)
            $users = User::whereIn('role', ['owner', 'super_admin', 'admin', 'cs', 'produksi'])->get();
            foreach ($users as $user) {
                $user->notify($notification);
            }
        } elseif ($recipients === 'owners') {
            // Kirim hanya ke owner & super_admin
            $users = User::whereIn('role', ['owner', 'super_admin'])->get();
            foreach ($users as $user) {
                $user->notify($notification);
            }
        } elseif (is_array($recipients)) {
            // Kirim ke daftar user ID
            $users = User::whereIn('id', $recipients)->get();
            foreach ($users as $user) {
                $user->notify($notification);
            }
        } else {
            // Kirim ke satu user ID
            $user = User::find($recipients);
            $user?->notify($notification);
        }
    }
}