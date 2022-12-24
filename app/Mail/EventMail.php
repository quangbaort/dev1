<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * @Deprecated use ConfirmAttendEventMail instead
 */
class EventMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        setlocale(LC_ALL, 'ja_JP');

        // 終日の場合、時刻を表示しない
        $fmt = 'YYYY年MM月DD日（dd） hh:mm';
        if($this->data->is_allday) {
            $fmt = 'YYYY年MM月DD日（dd）';
        }

        $start_obj = new Carbon($this->data->start);
        $start_fmt = $start_obj->isoFormat($fmt);

        $end_obj = new Carbon($this->data->end);
        $end_fmt = $end_obj->isoFormat($fmt);

        // 終日かつ開始日と終了日が同一の日の場合、終了日時を表示しない
        $this->data->event_time = $start_fmt . ' 〜 ' . $end_fmt;
        if($start_fmt === $end_fmt) {
            $this->data->event_time = $start_fmt;
        }

        $period_obj = new Carbon($this->data->period);
        $this->data->period_fmt = $period_obj->isoFormat('YYYY年MM月DD日（dd）');

        $this->data->join_url    = route('answerEvent', ['event' => $this->data->id, 'token' => $this->data->token, 'answer' => 1]);
        $this->data->on_hold_url = route('answerEvent', ['event' => $this->data->id, 'token' => $this->data->token, 'answer' => 3]);
        $this->data->deny_url    = route('answerEvent', ['event' => $this->data->id, 'token' => $this->data->token, 'answer' => 2]);

        $title = $start_obj->format('m/d') . ' ' . $this->data->title . 'の出欠確認';

        return $this->markdown('mail.html.event', ['data' => $this->data])->subject($title);
    }
}
