<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ConfirmAttendEventMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\Models\Event
     */
    protected $event;

    /**
     * @var string
     */
    protected $userId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($event, $userId)
    {
        $this->event = $event;
        $this->userId = $userId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $defaultUrlParameters = [
            'eid' => $this->event->id,
            'uid' => $this->userId,
            'answer' => EVENT_ANSWER_JOIN
        ];

        $urls = [
            'join' => URL::signedRoute(EVENT_RESPONSE_INVITE_ROUTE, $defaultUrlParameters),
            'hold' => URL::signedRoute(
                EVENT_RESPONSE_INVITE_ROUTE,
                array_merge($defaultUrlParameters, ['answer' => EVENT_ANSWER_HOLD])
            ),
            'deny' => URL::signedRoute(
                EVENT_RESPONSE_INVITE_ROUTE,
                array_merge($defaultUrlParameters, ['answer' => EVENT_ANSWER_REJECT])
            ),
        ];

        // 終日の場合、時刻を表示しない
        $fmt = ($this->event->is_allday) ? 'Y年m月d日（D）' : 'Y年m月d日（D）H:i';

        $start_fmt = $this->event->start->translatedFormat($fmt);
        $end_fmt = $this->event->end->translatedFormat($fmt);

        // 終日かつ開始日と終了日が同一の日の場合、終了日時を表示しない
        $this->event->event_time = ($start_fmt === $end_fmt) ? $start_fmt : $start_fmt . ' 〜 ' . $end_fmt;

        return $this
            ->markdown('mails.ja.event.confirmation', ['event' => $this->event, 'urls' => $urls])
            ->subject(
                trans(
                    'mail.event_confirm_subj',
                    ['name' => $this->event->name, 'date' => $this->event->start->format('m/d')]
                )
            );
    }
}
