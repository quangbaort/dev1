<?php

namespace App\Jobs;

use App\Repositories\OrganizationRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaveUserLogModelJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var string name of table for save log
     */
    protected $logTable = 'logs';

    /**
     * Activities on organization
     *
     * @var string
     */
    protected $organId;

    /**
     * @var Model model of action
     */
    protected $model;

    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * @var string Log type: create/edit/delete (1:追加、2:更新、3：削除)
     */
    protected $action;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($organId, $model, $logType, $user)
    {
        $this->organId = $organId;
        $this->model = $model;
        $this->action = $logType;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $date = Carbon::now();
        // set detail
        $detail = $date->format('Y/m/d') . ' ' ;
        if ($this->model->name) {
            $detail = $detail . $this->model->name;
        } elseif ($this->model->title) {
            $detail = $detail . $this->model->title;
        }

        // get function of screen
        $arrName = explode('\\', get_class($this->model));
        $className = array_pop($arrName);
        $function = trans('screen.' . $className . '.' . $this->action);

        //set organization name
        $organizationName = $this->getOrganizationName();

        // set function if change password
        if ($this->model->isDirty('password')) {
            $function = trans('screen.ChangePassword');
        }

        DB::table($this->logTable)->insert([
            'id'                => (string) Str::orderedUuid(),
            'user_id'           => $this->user->id,
            'user_name'         => $this->user->name,
            'date'              => $date,
            'organization_name' => $organizationName,
            'type'              => $this->action,
            'detail'            => $detail,
            'function'          => $function,
        ]);
    }

    /**
     * @return false|\Illuminate\Support\HigherOrderCollectionProxy|mixed
     */
    public function getOrganizationName()
    {
        try {
            $organ = app(OrganizationRepository::class)->find($this->organId, ['name']);
            if (is_null($organ)) {
                return false;
            }

            return $organ->name;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
