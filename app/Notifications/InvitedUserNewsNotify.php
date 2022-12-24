<?php

namespace App\Notifications;

use App\Mail\InviteUserNewsMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class InvitedUserNewsNotify extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\Models\News
     */
    protected $news;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($news)
    {
        $this->news = $news;
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
     * @param mixed $notifiable
     *
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail($notifiable)
    {
        return (new InviteUserNewsMail($this->news, $notifiable))->to($notifiable->email);
    }
}
