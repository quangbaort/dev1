<?php

namespace App\Jobs;

use App\Models\SafetyCheckResponse;
use App\Notifications\RemindAnswerSafetyCheckNotify;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SafetyCheckSendNotifyDaily implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * @var string
     */
    protected $safetyCheckId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($safetyCheckId = null)
    {
        $this->safetyCheckId = $safetyCheckId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sendAt = Carbon::now()->toDateString();

        $builder = SafetyCheckResponse::with(['user', 'safety'])
            ->where('notified_at', $sendAt)->whereNull('answered_at');
        if (!is_null($this->safetyCheckId)) {
            $builder = $builder->where('safety_check_id', $this->safetyCheckId);
        }

        $builder->orderBy('created_at')->chunk(2000, function ($responses) {
            foreach ($responses as $item) {
                $item->user->notify(
                    new RemindAnswerSafetyCheckNotify(
                        $item->safety,
                        $item->notified_at->toDateString()
                    )
                );
            }
        });
    }
}
