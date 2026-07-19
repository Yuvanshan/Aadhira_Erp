<?php

namespace App\Notifications;

use App\Utils\NotificationUtil;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BackupNotification extends Notification
{
    use Queueable;

    protected $filePath;
    protected $fileName;
    protected $business;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($filePath, $fileName, $business)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->business = $business;

        // Configure email dynamically using business email settings
        $notificationUtil = new NotificationUtil();
        $notificationUtil->configureEmail([
            'email_settings' => $business->email_settings
        ], true);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $downloadUrl = action([\App\Http\Controllers\BackUpController::class, 'download'], [$this->fileName]);

        $mailMessage = (new MailMessage)
            ->subject('Database Backup - ' . ($this->business->name ?? config('app.name')))
            ->greeting('Hello,')
            ->line('A new backup has been successfully generated for your ERP system.')
            ->line('Details:')
            ->line('Backup File: ' . $this->fileName)
            ->line('Date: ' . now()->toDateTimeString())
            ->action('Download Backup from ERP', $downloadUrl)
            ->line('You can also access all backups under Settings > Backups in the ERP.');

        // Attach the backup file if it exists and is local
        if ($this->filePath && file_exists($this->filePath)) {
            $mailMessage->attach($this->filePath, [
                'as' => $this->fileName,
                'mime' => 'application/zip',
            ]);
        }

        return $mailMessage;
    }
}
