<?php

namespace App\Mail;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class InviteUserNewsMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\Models\News
     */
    private $news;

    /**
     * @var \App\Models\User
     */
    private $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(News $news, $user)
    {
        $this->news = $news;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(trans('mail.news_invited_user', ['title' => $this->news->title]))
            ->markdown('mails.ja.news.invited_user', [
                'news'        => $this->news->loadMissing('file'),
                'urlMarkRead' => $this->news->urlMarkAsRead($this->news->id, $this->user->id),
            ]);
    }
}
