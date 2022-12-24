<?php

namespace App\Listeners;

use App\Notifications\InvitedUserNewsNotify;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNoticeNewsListener implements ShouldQueue
{
    use InteractsWithQueue;
    use SerializesModels;

    /**
     * Handle the event.
     *
     * @param $event
     *
     * @return void
     */
    public function handle($event)
    {
        $newsModel = $event->news;
        $unReadUsers = $newsModel->users()->wherePivotNull('read_at')->get();
        if ($unReadUsers->isEmpty()) {
            return;
        }

        foreach ($unReadUsers as $user) {
            $user->notify(new InvitedUserNewsNotify($newsModel));
        }
    }
}
