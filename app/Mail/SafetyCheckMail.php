<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SafetyCheckMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\Models\SafetyCheck
     */
    private $safetyCheck;

    /**
     * @var string Date format: Y-m-d
     */
    private $notifyAt;

    /**
     * @var string
     */
    private $userId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($safetyCheck, $userId, $notifyAt)
    {
        $this->safetyCheck = $safetyCheck;
        $this->notifyAt = $notifyAt;
        $this->userId = $userId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $urls = [
            'normal' => $this->safetyCheck->answerUrl(
                $this->safetyCheck->id,
                $this->userId,
                $this->notifyAt,
                SAFETY_ANSWER_SAFE
            ),
            'urgent' => $this->safetyCheck->answerUrl(
                $this->safetyCheck->id,
                $this->userId,
                $this->notifyAt,
                SAFETY_ANSWER_NG
            ),
        ];

        return $this->subject($this->safetyCheck->title)
            ->markdown('mails.ja.safety_check.answer', [
                'safetyCheck' => $this->safetyCheck,
                'urls' => $urls
            ]);
    }
}
