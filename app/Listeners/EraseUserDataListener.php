<?php

namespace App\Listeners;

use App\Models\NotifyRecipientGroup;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EraseUserDataListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        $user = $event->user;
        $organId = $event->organId;

        $this->notifyGroups($user->id, $organId);
    }

    /**
     * @param $userId
     * @param $organId
     */
    protected function notifyGroups($userId, $organId)
    {
        $groupHasUser = NotifyRecipientGroup::ofOrgan($organId)->whereHas('users', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->get();

        foreach ($groupHasUser as $group) {
            $group->users()->detach($userId);
        }
    }
}
