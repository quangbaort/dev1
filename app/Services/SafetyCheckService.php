<?php

namespace App\Services;

use App\Events\RemindAnswerSafetyEvent;
use App\Events\SafetyCheckCreatedEvent;
use App\Events\SafetyCheckUpdatedEvent;
use App\Helpers\Facades\Helper;
use App\Repositories\SafetiesCheckRepository;
use App\Services\Concerns\BaseService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SafetyCheckService extends BaseService
{
    /**
     * @param \App\Repositories\SafetiesCheckRepository $repository
     */
    public function __construct(SafetiesCheckRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Auto paginate with query parameters
     *
     * @param array search conditions
     *
     * @return mixed
     */
    public function search($conditions = [])
    {
        // Clean parameters in request
        $this->makeBuilder($conditions);

        $organId = Helper::organId();
        $requester = $this->requestHttp->user();

        $this->builder->ofOrgan($organId)->with(['department', 'responses']);

        if (!$requester->isAdminHigher()) {
            $this->builder->responseOfUser($requester->id)->with(['responses' => function ($q) use ($requester) {
                $q->ofUser($requester->id);
            }]);
            $this->builder->whereHas('responses', function ($q) {
                $q->where('notified_at', Carbon::now()->format('Y/m/d'));
            });
        }else{
            $this->builder->with(['responses' => function ($q) {
                $q->where('notified_at', '<=',Carbon::now()->format('Y/m/d'));
            }]);
        }


        if ($this->filter->has('title')) {
            $this->builder->where('title', 'LIKE', '%' . $this->filter->get('title') . '%');
            $this->cleanFilterBuilder('title');
        }

        if ($this->filter->has('department')) {
            $this->builder->whereHas('department', function ($q) use ($organId) {
                $q->ofOrgan($organId)
                    ->where(function ($subQ) {
                        $subQ->orWhere('name', 'LIKE', '%' . $this->filter->get('department') . '%')
                            ->orWhere('name_kana', 'LIKE', '%' . $this->filter->get('department') . '%');
                    });
            });
        }

        return $this->endFilter();
    }

    /**
     * get detail data to database from request
     *
     * @param string $id
     * @param string $organId
     *
     * @return mixed
     * @throws \Throwable
     */
    public function detail($id, $organId)
    {
        $requester = app('request')->user();
        $loadRelations = ['users' => function ($q) use ($requester) {
            $q->ofUser($requester->id);
        }];

        if ($requester->isAdminHigher()) {
            $loadRelations = ['users' => function ($q) use ($organId) {
                $q->withCompany($organId);
            }];
        }

        $safety = $this->repository->detail($id, $organId, $loadRelations);

        //authentication for normal user
        throw_if(!$requester->isAdminHigher(), AuthorizationException::class);

        return $safety;
    }

    /**
     * Save data to database from request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed|void
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $id = $request->input('id');
        // Check parameter name ID from request.
        // If ID is empty, action is create a new record
        if (!$id) {
            $this->authorize('create', $request->user());

            return $this->insert($request);
        }

        $this->authorize('update', $request->user());

        return $this->update($request);
    }

    /**
     * Get statistic of safety
     *
     * @param string $id
     * @param string $organId
     *
     * @return null|\Illuminate\Database\Eloquent\Model
     * @throws \Throwable
     */
    public function chart($id, $organId)
    {
        $request = app('request');
        $requester = $request->user();

        throw_if(!$requester->isAdminHigher(), AuthorizationException::class);

        // Clean parameters in request
        $this->makeBuilder(['include' => null]);
        $this->builder->ofOrgan($organId);
        $this->builder->with(['responses', 'responses.user', 'responses.user.companies']);
        $this->builder->with(['responses' => function ($q) use ($request) {
            if ($this->filter->has('startDate')) {
                $q = $q->where('answered_at', '>=', Carbon::parse($request->input('startDate'))->startOfDay());
            }

            if ($this->filter->has('endDate')) {
                $q = $q->where('answered_at', '<=', Carbon::parse($request->input('endDate'))->endOfDay());
            }
            if ($this->filter->has('name')) {
                $q->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $this->filter->get('name') . '%');
                });
            }
            if ($this->filter->has('company')) {
                $q->whereHas('user.companies', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $this->filter->get('company') . '%');
                });
            }
        }]);

        return $this->builder->findOrFail($id);
    }

    /**
     * delete
     *
     * @param  mixed $id: id of safety
     * @return true if delete success
     */
    public function destroy($id, $organId)
    {
        //get data safety to db
        $safety = $this->repository->detail($id, $organId, []);

        // delete safety replies
        $safety->responses()->delete();

        // delete safety
        $safety->delete();

        return true;
    }

    /**
     * delete multiple safeties selected
     *
     * @param mixed array id of safeties
     * @return true if delete success
     */
    public function destroyMulti($request)
    {
        foreach ($request->ids as $safetyId) {
            $this->destroy($safetyId);
        }
        return true;
    }

    /**
     * @param string $safetyId
     * @param string $userId
     * @param string $notifyAt Date format: Y-m-d
     * @param int $answer
     * @param null $comment
     *
     * @return bool
     */
    public function answer($safetyId, $userId, $notifyAt, $answer = SAFETY_ANSWER_SAFE, $comment = null)
    {
        $response = $this->repository->responseOfUser($safetyId, $userId, $notifyAt);

        $response->answered_at = Carbon::now()->toDateTimeString();
        $response->answer = $answer;
        $response->comment = $comment;
        $response->save();

        return $this->withSuccess(trans('message.answer_safety_success'));
    }

    /**
     * @param $safetyId
     * @param $responseForUser
     * @param $notifyAt
     * @param int $responseSts
     *
     * @return bool
     */
    public function responseAnswer($safetyId, $responseForUser, $notifyAt, $responseSts = SAFETY_SUPPORTED)
    {
        $response = $this->repository->responseOfUser($safetyId, $responseForUser, $notifyAt);
        $response->response = $responseSts;
        $response->save();

        return $this->withSuccess(trans('message.answer_safety_success'));
    }

    /**
     * Send remind answer safety check
     *
     * @param string $safetyId
     * @param string $dateRemind Date string. Format: Y-m-d
     */
    public function remindAnswer($safetyId, $dateRemind)
    {
        $safety = $this->repository->detail($safetyId, Helper::organId(), []);

        RemindAnswerSafetyEvent::dispatch($safety, $dateRemind);

        return $safety;
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

    /**
     * Insert data safety
     *
     * @param Request $request
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    private function insert(Request $request)
    {
        $data = $request->toArray();
        $data['organization_id'] = $request->organization->get('id');

        $record = $this->repository->create($data);

        $repeater = new RepeatDateHelper(
            $request->input('repeat_start'),
            $request->input('repeat_end'),
            $request->input('users')
        );

        $users = $this->mixCaseRepeater(
            $repeater,
            $request->input('repeat'),
            $request->only(['repeat_week', 'repeat_date'])
        );

        if (!empty($users)) {
            $record->responses()->createMany($users);
        }

        SafetyCheckCreatedEvent::dispatch($record);

        return $record;
    }

    /**
     * Update data safety
     *
     * @param Request $request
     *
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    private function update(Request $request)
    {
        //Find and check exits
        $safety = $this->repository->detail($request->input('id'), Helper::organId(), []);

        $repeater = new RepeatDateHelper(
            $request->input('repeat_start'),
            $request->input('repeat_end'),
            $request->input('users')
        );
        //Remove repeat
        if ($safety->repeat != NO_REPEAT && $request->input('repeat', NO_REPEAT) == NO_REPEAT) {
            //Clear all saved response
            $safety->responses()->delete();

            $safety->responses()->createMany($repeater->immediate());
            SafetyCheckUpdatedEvent::dispatch($safety, $request->input('resend', false));

            return $this->repository->updateByModel($safety, $request->except('users'));
        }

        // No update repeat time
        if ($this->submitNotUpdateRepeat($safety, $request)) {
            //check users and update
            $safety->responses()->whereNotIn('user_id', $request->input('users'))->delete();

            $existsUsers = $safety->responses()->selectRaw('distinct(`user_id`)')->pluck('user_id')->toArray();
            $newsInsertUsers = array_diff($request->input('users'), $existsUsers);
            if (!empty($newsInsertUsers)) {
                $repeatResponses = $this->mixCaseRepeater(
                    $repeater->updateUserMapping($newsInsertUsers),
                    $request->input('repeat'),
                    $request->only(['repeat_week', 'repeat_date'])
                );

                $safety->responses()->createMany($repeatResponses);
            }

            SafetyCheckUpdatedEvent::dispatch($safety, $request->input('resend', false));

            return $this->repository->updateByModel($safety, $request->except('users'));
        }

        $safety->responses()->delete();

        //Append repeat records
        $repeatResponses = $this->mixCaseRepeater(
            $repeater,
            $request->input('repeat'),
            $request->only(['repeat_week', 'repeat_date'])
        );

        if (!empty($repeatResponses)) {
            $safety->responses()->createMany($repeatResponses);
        }

        SafetyCheckUpdatedEvent::dispatch($safety, $request->input('resend', false));

        return $this->repository->updateByModel($safety, $request->except('users'));
    }

    /**
     * @param $safety
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    private function submitNotUpdateRepeat($safety, Request $request)
    {
        $requestRepeat = $request->input('repeat');
        if (
            ($requestRepeat == REPEAT_EVERYDAY && $safety->repeat == REPEAT_EVERYDAY)
            || ($requestRepeat == REPEAT_WEEK && $request->repeat_week == $safety->repeat_week)
            || ($requestRepeat == REPEAT_DATE && $request->repeat_date == $safety->repeat_date)
        ) {
            // Because format from request diff database
            return Carbon::parse($request->input('repeat_start'))->isSameDay($safety->repeat_start)
                && Carbon::parse($request->input('repeat_end'))->isSameDay($safety->repeat_end);
        }

        return false;
    }

    /**
     * @param $repeater
     * @param $repeatType
     * @param array $requestValues
     *
     * @return mixed
     */
    private function mixCaseRepeater($repeater, $repeatType, $requestValues = [])
    {
        return match ((int)$repeatType) {
            NO_REPEAT => $repeater->immediate(),
            REPEAT_EVERYDAY => $repeater->daily(),
            REPEAT_WEEK => $repeater->dayOfWeek(explode(',', $requestValues['repeat_week'])),
            REPEAT_DATE => $repeater->dayOfMonth($requestValues['repeat_date']),
        };
    }
}
