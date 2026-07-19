<?php

namespace App\Listeners;

use Spatie\Backup\Events\BackupWasSuccessful;
use App\Business;
use App\Notifications\BackupNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Log;

class SendBackupNotification
{
    /**
     * Handle the event.
     *
     * @param  BackupWasSuccessful  $event
     * @return void
     */
    public function handle(BackupWasSuccessful $event)
    {
        try {
            $newestBackup = $event->backupDestination->newestBackup();
            if (!$newestBackup) {
                return;
            }

            $disk = $newestBackup->disk();
            $path = $newestBackup->path();
            
            // Get the absolute path on disk if it's local
            $absolutePath = null;
            try {
                $absolutePath = $disk->path($path);
            } catch (\Exception $e) {
                // Not a local driver or doesn't support path()
            }

            $fileName = basename($path);

            // Determine recipient businesses
            $businesses = [];
            if (auth()->check()) {
                // Manual backup triggered by an authenticated user
                $businessId = request()->session()->get('user.business_id');
                if ($businessId) {
                    $business = Business::find($businessId);
                    if ($business && !empty($business->common_settings['backup_email'])) {
                        $businesses[] = $business;
                    }
                }
            } else {
                // Scheduled auto backup
                // Find all businesses with backup_email and backup_auto enabled
                $allBusinesses = Business::all();
                foreach ($allBusinesses as $b) {
                    $email = !empty($b->common_settings['backup_email']) ? $b->common_settings['backup_email'] : null;
                    $auto = !empty($b->common_settings['backup_auto']) ? $b->common_settings['backup_auto'] : null;
                    if ($email && $auto == 1) {
                        $businesses[] = $b;
                    }
                }
            }

            foreach ($businesses as $business) {
                $email = $business->common_settings['backup_email'];
                
                // Check if mail settings are configured to avoid TypeError
                $mailHost = !empty($business->email_settings['mail_host']) ? $business->email_settings['mail_host'] : config('mail.mailers.smtp.host');
                if (empty($mailHost)) {
                    Log::warning("Backup notification not sent to {$email} because SMTP host is not configured under Settings.");
                    continue;
                }

                // Send the backup email
                Notification::route('mail', $email)
                    ->notify(new BackupNotification($absolutePath, $fileName, $business));
            }
        } catch (\Throwable $e) {
            Log::error('Backup notification sending failed: ' . $e->getMessage());
        }
    }
}
