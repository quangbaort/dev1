<?php

namespace App\Services;

use App\Events\EventCreatedEvent;
use App\Events\RemindConfirmAttendEvent;
use App\Helpers\Facades\FileManager;
use App\Helpers\Facades\Helper;
use App\Repositories\EventRepository;
use App\Services\Concerns\BaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventService extends BaseService
{
    /**
     * @param \App\Repositories\EventRepository $repository
     */
    public function __construct(
        EventRepository $repository,
    ) {
        $this->repository = $repository;
    }

    /**
     * Auto paginate with query parameters
     *
     * @param array $conditions
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search($conditions = [])
    {
        // Clean parameters in request
        $this->makeBuilder($conditions);

        // Logged in user and request get list of events
        $requester = $this->requestHttp->user();
        $departmentRequest = $this->filter->get('board_id', []);

        // Default builder
        $this->useSimplePage()->ofOrgan($this->requestHttp->organization->get('id'))
            ->inPeriod(Carbon::parse($this->filter->get('start')), Carbon::parse($this->filter->get('end')))
            ->where(function ($q) use ($departmentRequest, $requester) {
                $q->ofDepartment($departmentRequest)
                ->orWhereHas('users', function ($q) use ($requester) {
                    $q->where('users.id', $requester->id);
                });
            })->withCount(['users as invited' => function ($q) use ($requester) {
                $q->where('users.id', $requester->id);
            }]);

        $this->cleanFilterBuilder(['start', 'end', 'board_id']);
        return $this->endFilter();
    }

    /**
     * @param string $eventId
     * @param string $organId
     * @param \App\Models\User $requester
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function detail($eventId, $organId, $requester)
    {
        $loadRelationships = ($requester->isAdminHigher())
            ? [
                'files',
                'users' => function ($q) use ($organId) {
                    $q->withCompany($organId);
                 },
                'departments'
            ]
            : ['files'];
        $event = ($requester->isAdminHigher())
            ? $this->find($eventId, $loadRelationships)
            : $this->repository->findEventAndUserInvited($eventId, $requester->id, $organId, $loadRelationships);

        $this->authorize('view', [$requester, $event]);

        return $event;
    }

    /**
     * Save data to database from request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Database\Eloquent\Collection|mixed
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        if (!$request->input('id')) {
            return $this->create($request);
        }

        return $this->update($request);
    }

    /**
     * Create a new event/meeting
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Database\Eloquent\Collection|mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    protected function create(Request $request)
    {
        $this->authorize('create', $this->getModel());

        // Create a new event
        $event = $this->repository->create($request->toArray());

        //Add event to boards
        $event->departments()->attach($request->input('departments'));

        //Event participants
        $event->users()->attach($request->input('users'));

        //Fire event
        EventCreatedEvent::dispatch($event);

        // Upload files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                FileManager::ofModel($event)->upload($file);
            }
        }
        return $event;
    }

    /**
     * Update event
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException|\Throwable
     */
    public function update(Request $request)
    {
        $event = $this->find($request->input('id'));
        // Check permission
        $this->authorize('update', $event);

        // update data event
        $eventUpdate = $this->repository->updateByModel($event, $request->toArray());

        //update data event ref boards
        $eventUpdate->departments()->sync($request->input('departments', []));

        //update data event notify reply
        $eventUpdate->users()->sync($request->input('users', []));

        //Resend notification
        if ($request->filled('resend')) {
            EventCreatedEvent::dispatch($eventUpdate);
        }

        //Remove files
        $eventFiles = $eventUpdate->files;
        $keepFiles = $request->input('old_file', []);
        $deleteFiles = empty($keepFiles) ? $eventFiles : $eventFiles->whereNotIn('id', $keepFiles);
        if (!$eventFiles->isEmpty() && !$deleteFiles->isEmpty()) {
            FileManager::ofModel($eventUpdate)->delete($deleteFiles);
        }

        // Upload files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                FileManager::ofModel($eventUpdate)->upload($file);
            }
        }

        return $eventUpdate;
    }

    /**
     * delete
     *
     * @param  mixed $id: id of Event
     * @return true if delete success
     */
    public function delete($id)
    {
        $event = $this->find($id);
        // delete data ref boards
        $event->departments()->detach();
        //delete data event notify reply
        $event->users()->detach();
        // delete data event
        $event->delete($id);

        return true;
    }

    /**
     * Get data chart events
     *
     * @param $eventId
     *
     * @return array
     */
    public function chart($eventId)
    {
        $request = app('request');
        $organId = Helper::organId();
        $validates = [
            'start' => 'date',
            'end' => 'date|after_or_equal:start',
        ];
        $request->validate($validates);

        $builder = $this->repository->getModel()->where('id', $eventId);
        $builder = $builder->with(['users' => function ($q) use ($request, $organId) {
            if ($request->filled('start')) {
                $q = $q->where('answered_at', '>=', Carbon::parse($request->input('start'))->startOfDay());
            }

            if ($request->filled('end')) {
                $q = $q->where('answered_at', '<=', Carbon::parse($request->input('end'))->endOfDay());
            }

            if ($request->filled('name')) {
                $q = $q->where(function ($subQ) use ($request) {
                    $subQ->where('users.name', 'LIKE', '%' . $request->input('name') . '%')
                        ->orWhere('users.name_kana', 'LIKE', '%' . $request->input('name') . '%');
                });
            }

            $q = $q->whereHas('companies', function ($subQ) use ($request) {
                if ($request->filled('company')) {
                    $subQ->where('companies.name', 'LIKE', '%' . $request->input('company') . '%')
                        ->orWhere('companies.name_kana', 'LIKE', '%' . $request->input('company') . '%');
                }
            })->withCompany($organId);

            return $q;
        }]);

        $event = $builder->first();

        $totalUsers = $event->users->count();

        $event->unanswered = ($totalUsers == 0) ? 0 : ceil(
            $event->users->where('pivot.answer', 0)->count() / $totalUsers * 100
        );
        $event->attendance = ($totalUsers == 0) ? 0 : ceil(
            $event->users->where('pivot.answer', EVENT_ANSWER_JOIN)->count() / $totalUsers * 100
        );
        $event->absence = ($totalUsers == 0) ? 0 : ceil(
            $event->users->where('pivot.answer', EVENT_ANSWER_HOLD)->count() / $totalUsers * 100
        );
        $event->pending = ($totalUsers == 0) ? 0 : ceil(
            $event->users->where('pivot.answer', EVENT_ANSWER_REJECT)->count() / $totalUsers * 100
        );
        $event->totalAnswer = ($totalUsers == 0) ? 0 : ceil(
            $event->users->where('pivot.answer', '!=', 0)->count() / $totalUsers * 100
        );

        return $event;
    }

    /**
     * User answer event invited
     *
     * @param string $eventId
     * @param int $answered
     */
    public function inviteAnswer($eventId, $userId, $answered = EVENT_ANSWER_JOIN)
    {
        //Validate answer
        abort_if(!in_array($answered, [EVENT_ANSWER_JOIN, EVENT_ANSWER_HOLD, EVENT_ANSWER_REJECT]), 404);

        $event = $this->repository->findEventAndUserInvited($eventId, $userId);

        if (is_null($event) || $event->isAnswerTimeEnd()) {
            return $this->withErrors(trans('app.answer_fail'));
        }

        $event->users()->updateExistingPivot($userId, ['answered_at' => now(), 'answer' => $answered]);

        return $this->withSuccess(trans('app.answer_success'));
    }

    /**
     * Resend notify remind answer event
     *
     * @param string $eventId
     */
    public function remindAnswerInvited($eventId)
    {
        $event = $this->repository->detail($eventId, Helper::organId());

        RemindConfirmAttendEvent::dispatch($event);

        return $event;
    }

    /**
     * Count notify unread of user
     *
     * @return int
     */
    public function countUnread($organId, $userId)
    {
        return $this->repository->totalUnread($organId, $userId);
    }
}
