<?php

namespace App\Notifications;

use App\Mail\SafetyCheckMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RemindAnswerSafetyCheckNotify extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var \App\Models\SafetyCheck
     */
    protected $safetyCheck;

    /**
     * @var string Column notify_at in safety_check_responses
     */
    protected $notifyAt;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($safetyCheck, $notifyAt)
    {
        $this->safetyCheck = $safetyCheck;
        $this->notifyAt = $notifyAt;
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
     * @param   $notifiable
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail($notifiable)
    {
        return (new SafetyCheckMail($this->safetyCheck, $notifiable->id, $this->notifyAt))->to($notifiable->email);
    }
}
