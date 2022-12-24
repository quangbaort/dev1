<?php

namespace App\Services;

use App\Events\NewsCreatedEvent;
use App\Events\NewsUpdatedEvent;
use App\Events\ResendRemindNewsEvent;
use App\Helpers\Facades\FileManager;
use App\Helpers\Facades\Helper;
use App\Models\NewsResponse;
use App\Repositories\NewsRepository;
use App\Repositories\UserRepository;
use App\Services\Concerns\BaseService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NewsService extends BaseService
{
    /**
 * @var \App\Repositories\UserRepository
 */
    protected $userRepository;

    /**
     * @param NewsRepository $repository
     * @param \App\Repositories\UserRepository $userRepository
     */
    public function __construct(NewsRepository $repository, UserRepository $userRepository)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    /**
     * Search logic
     */
    public function search($conditions = [])
    {
        // Clean parameters in request
        $this->makeBuilder($conditions);

        $requester = $this->requestHttp->user();

        $this->builder
            ->with(['department', 'users'])
            ->ofOrgan(Helper::organId());

        $this->builder->when($this->filter->has('board_id'), function () {
            $this->builder->ofDepartment($this->filter->get('board_id'));
        });

        if (!$requester->isAdminHigher()) {
            $this->builder
                ->whereHas('users', function ($q) use ($requester) {
                    $q->where('users.id', $requester->id);
                })
                ->with(['users' => function ($q) use ($requester) {
                    $q->where('users.id', $requester->id);
                }]);
        }

        if ($this->filter->has('start') && $this->filter->has('end')) {
            $this->builder->inPeriod(
                Carbon::parse($this->filter->get('start')),
                Carbon::parse($this->filter->get('end'))
            );
            $this->cleanFilterBuilder(['start', 'end']);
        }

        /**
         * When users search by title, need to search with LIKE condition and process all records
         */
        if ($this->filter->has('title')) {
            $this->builder->where('title', 'LIKE', '%' . $this->filter->get('title') . '%');
            // Remove condition after apply query builder
            $this->cleanFilterBuilder('title');
        }

        if ($this->filter->has('department_name')) {
            $this->builder->whereHas('department', function (Builder $query) {
                $query->where('name', 'LIKE', '%' . $this->filter->get('department_name') . '%');
            });
        }

        return $this->endFilter();
    }

    /**
     * Get detail news of organization
     *
     * @param string $newsId
     * @param string $organId
     *
     * @return mixed
     * @throws \Throwable
     */
    public function detail($newsId, $organId)
    {
        $requester = app('request')->user();
        $loadRelations = ['users' => function ($q) use ($requester) {
            $q->where('users.id', $requester->id);
        }];
        if ($requester->isAdminHigher()) {
            $loadRelations = ['file', 'department', 'users' => function ($q) use ($organId) {
                $q->withCompany($organId);
            }];
        }

        $news = $this->repository->detail($newsId, $organId, $loadRelations);

        $invitedData = $news->users->firstWhere('pivot.user_id', $requester->id);

        //authentication for normal user
        throw_if(!$requester->isAdminHigher() && is_null($invitedData), AuthorizationException::class);

        if (!is_null($invitedData)) {
            $news->can_response = is_null($invitedData->pivot->read_at);
            $news->url_mark_read = $news->urlMarkAsRead($news->id, $requester->id);
        }

        return $news;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $requester = $request->user();
        $id = $request->input('id');
        $requestData = $request->except(['image', 'file']);
        $requestData['department_id'] = $request->input('board_id');

        // Check parameter name ID from request.
        // If ID is empty, action is create a new record
        if (!$id) {
            $this->authorize('create', $requester);

            //upload image
            if ($request->hasFile('image')) {
                $requestData['image_s3_url'] = FileManager::path(FILE_TYPE_NEWS)->upload($request->image);
            }
            //Add to table news
            $news = $this->repository->create($requestData);

            //Add to table news_replies
            $news->users()->attach($request->input('user_id'));

            //upload file
            if ($request->hasFile('file')) {
                FileManager::ofModel($news)->withName($request->input('file_name'))->upload($request->file);
            }

            NewsCreatedEvent::dispatch($news);

            return $news;
        }

        // Get record from DB and check exits
        $record = $this->find($id, ['file']);

        // update image
        if ($request->hasFile('image')) {
            $requestData['image_s3_url'] = FileManager::path(FILE_TYPE_NEWS)
                ->uploadAndRemove($request->image, $record->image_s3_url);
        } elseif (!$request->filled('old_image')) {
            FileManager::delete($record->image_s3_url);
            $requestData['image_s3_url'] = null;
        }

        // update file
        if ($request->hasFile('file')) {
            FileManager::ofModel($record)
                ->withName($request->input('file_name'))
                ->uploadAndRemove($request->file, $record->file);
        } elseif (!$request->filled('old_file')) {
            FileManager::delete($record->file);
        } elseif ($record->file && $request->input('file_name') != $record->file->name) {
            FileManager::ofModel($record)->changeFileName($record->id, $request->input('file_name'));
        }

        // Handle update record to DB
        $recordUpdated = $this->repository->updateByModel($record, $requestData);

        if ($request->input('resend', false)) {
            $recordUpdated->users()->syncWithPivotValues($request->input('user_id'), ['read_at' => null]);
            ResendRemindNewsEvent::dispatch($recordUpdated);
        } else {
            $recordUpdated->users()->sync($request->input('user_id'));
        }

        //Fire event
        NewsUpdatedEvent::dispatch($record);

        return $recordUpdated;
    }

    /**
     * Delete news by id
     *
     * @param  string $id
     * @return true delete success
     */
    public function delete($id)
    {
        $news = $this->find($id);

        // Delete image
        FileManager::delete($news->image_s3_url);

        // Delete file
        FileManager::delete($news->file);

        $news->users()->detach();

        return $news->delete();
    }

    /**
     * Get statistic of news
     *
     * @param string $id
     * @param string $organId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function chart($id, $organId)
    {
        $request = app('request');

        //Get detail with relationship and validation
        $news = $this->repository->detail($id, $organId, 'users');
        $this->authorize('statistic', $news);

        $receivers = $news->users;
        $pivotColumn = 'pivot.read_at';

        if ($request->filled('start_read')) {
            $receivers = $receivers
                ->where($pivotColumn, '>=', Carbon::parse($request->input('start_read'))->startOfDay());
        }

        if ($request->filled('end_read')) {
            $receivers = $receivers
                ->where($pivotColumn, '<=', Carbon::parse($request->input('end_read'))->endOfDay());
        }

        return [
            'total' => $news->users->count(),
            'sub_total' => $receivers->count(),
            'read' => $receivers->whereNotNull($pivotColumn)->count(),
            'unread' => $receivers->whereNull($pivotColumn)->count()
        ];
    }

    /**
     * Get all users and status's response
     *
     * @param string $newsId
     * @param string $organId
     *
     */
    public function users($newsId, $organId)
    {
        $request = app('request');

        $builder = NewsResponse::with('user.companies')
//            ->where('organization_id', $organId)
            ->whereHas('news', function ($q) use ($newsId, $organId) {
            $q->where('news.id', $newsId)->ofOrgan($organId);
        });

        if ($request->filled('start_read')) {
            $builder = $builder->where('read_at', '>=', Carbon::parse($request->input('start_read'))->startOfDay());
        }

        if ($request->filled('end_read')) {
            $builder = $builder->where('read_at', '<=', Carbon::parse($request->input('end_read'))->endOfDay());
        }


        if ($request->filled('user_name')) {
            $builder = $builder->whereHas('user', function ($q) use ($request) {
                $q->where('users.name', 'LIKE', '%' . $request->input('user_name') . '%')
                    ->orWhere('users.name_kana', 'LIKE', '%' . $request->input('user_name') . '%');
            });
        }

        if ($request->filled('company_name')) {
            $builder = $builder->whereHas('user.companies', function ($q) use ($request) {
                $q->where('companies.name', 'LIKE', '%' . $request->input('company_name') . '%')
                    ->orWhere('users.name_kana', 'LIKE', '%' . $request->input('company_name') . '%');
            });
        }

        return $builder->paginate($request->input('limit', PAGE_SIZE))->appends($request->all());
    }

    /**
     * Send notify remind to users doesn't read news
     *
     * @param string $id
     * @param string $organId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function resendRemind($id, $organId)
    {
        //Check exits record
        $news = $this->repository->detail($id, $organId, []);

        $this->authorize('statistic', $news);

        ResendRemindNewsEvent::dispatch($news);

        return $news;
    }

    /**
     * User mark as read news
     *
     * @param string $newsId
     * @param string $userId
     *
     * @return bool
     */
    public function markAsRead($newsId, $userId)
    {
        $responseStatus = $this->repository->responseOfUser($newsId, $userId);

        if (!is_null($responseStatus->read_at)) {
            return $this->withErrors(trans('message.news_already_read'));
        }

        $responseStatus->read_at = now();
        $responseStatus->save();

        return $this->withSuccess(trans('message.news_marked_read'));
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
