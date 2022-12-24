<?php

namespace Database\Factories;

use App\Models\NotifyRecipientGroup;
use App\Models\NotifyRecipient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class NotifyUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NotifyRecipient::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $notifyGroups = NotifyRecipientGroup::orderBy('created_at', 'ASC')->get();
            if(empty($notifyGroups)) {
            return $this->defaultNotifyUser();
        }

        $notifyGroup = $this->faker->randomElement($notifyGroups);
        $organizationId = $notifyGroup->organization_id;

        $existsUserIds = NotifyRecipient::where('notify_group_id', $notifyGroup->id)->pluck('user_id');
        $query = User::select('users.id as user_id')->leftJoin('belongs', 'belongs.user_id', 'users.id')
                ->leftJoin('departments', 'belongs.board_id', 'departments.id')
                ->where('departments.organization_id', $organizationId);
        if(empty($existsUserIds)) {
            $query->whereNotIn('users.id', $existsUserIds);
        }
        $users = $query->get();

        if(empty($users)) {
            $arr = $this->defaultNotifyUser();
            return $arr;
        }
        $user = $this->faker->randomElement($users);
        if(!$user) {
            return $this->defaultNotifyUser();
        }

        return [
            'notify_group_id' => $notifyGroup->id,
            'user_id' => $user->user_id
        ];
    }

    private function defaultNotifyUser() {
        return [
            'notify_group_id' => $this->faker->uuid(),
            'user_id' => $this->faker->uuid()
        ];
    }
}
