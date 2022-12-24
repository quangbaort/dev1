<?php

namespace App\Services;

use App\Events\OrganAttachedUserEvent;
use App\Events\UserCreatedEvent;
use App\Events\UserDetachedOrganEvent;
use App\Exports\UserExport;
use App\Helpers\AppHelpers;
use App\Helpers\Facades\FileManager;
use App\Helpers\Facades\Helper;
use App\Repositories\OrganizationRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use App\Services\Concerns\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

class UserService extends BaseService
{
    /**
     * @var \App\Repositories\UserRoleRepository
     */
    protected $userRoleRepository;

    /**
     * @var \App\Repositories\OrganizationRepository
     */
    protected $organRepository;

    /**
     * @param \App\Repositories\UserRepository $repository
     * @param \App\Repositories\UserRoleRepository $userRoleRepository
     * @param \App\Services\UserRoleService $userRoleService
     * @param \App\Services\UserCompanyService $userCompanyService
     * @param \App\Repositories\OrganizationRepository $organizationRepository
     * @param \App\Helpers\AppHelpers $appHelpers
     */
    public function __construct(
        UserRepository $repository,
        UserRoleRepository $userRoleRepository,
        UserRoleService $userRoleService,
        UserCompanyService $userCompanyService,
        OrganizationRepository $organizationRepository,
        AppHelpers $appHelpers
    ) {
        $this->repository               = $repository;
        $this->userRoleRepository = $userRoleRepository;
        $this->userRoleService          = $userRoleService;
        $this->userCompanyService       = $userCompanyService;
        $this->organizationRepository   = $organizationRepository;
        $this->appHelpers               = $appHelpers;
        $this->organRepository = $organizationRepository;
    }

    /**
     * Get user detail and load relationships
     *
     * @param string $userId
     */
    public function detail($userId, $organId)
    {
        return $this->repository->detail($userId, $organId);
    }

    /**
     * Create filter and response list by conditions
     *
     * @param array $conditions
     *
     * @return mixed
     */
    public function search($conditions = [])
    {
        // list user of admin
        $this->makeBuilder($conditions);
        $organId = Helper::organId();
        $this->builder->whereHas('organizations', function ($q) use ($organId) {
            $q->where('organizations.id', $organId);
        });
        $this->builder
            ->withCompany($organId)
            ->withDepartment($organId);

        if ($this->filter->has('board_id')) {
            $this->builder->whereHas('departments', function (Builder $query) {
                $query->whereIn('id', $this->filter->get('board_id'));
            });
            // Remove condition after apply query builder
            $this->cleanFilterBuilder('board_id');
        }
        if($this->filter->has('company_id')) {
            $this->builder->whereHas('companies', function (Builder $query) {
                $query->where('id', $this->filter->get('company_id'));
            });
            // Remove condition after apply query builder
            $this->cleanFilterBuilder('company_id');
        }

        //Search by notify recipient group
        if ($this->filter->has('notify_group_id')) {
            $this->builder->whereHas('notifyGroups', function ($q) {
                $q->where('group_id', $this->filter->get('notify_group_id'));
            });
        }

        /**
         * When users search, need to search with LIKE condition and process all records
         */
        if ($this->filter->has('name')) {
            $this->builder->where(function ($q) {
                $q->where('name', 'LIKE', '%' . $this->filter->get('name') . '%')
                    ->orWhere('name_kana', 'LIKE', '%' . $this->filter->get('name') . '%');
            });
            // Remove condition after apply query builder
            $this->cleanFilterBuilder('name');
        }

        $this->builder->whereHas('companies', function (Builder $query) {
            if ($this->filter->has('company')) {
                $query = $query->where(function ($subQ) {
                    $subQ->where('name', 'LIKE', '%' . $this->filter->get('company') . '%')
                        ->orWhere('name_kana', 'LIKE', '%' . $this->filter->get('company') . '%');
                });
            }

            if ($this->filter->has('prefecture')) {
                $query = $query->where('prefecture', $this->filter->get('prefecture'));
            }

            if ($this->filter->has('city')) {
                $query = $query->where('city', 'LIKE', '%' . $this->filter->get('city') . '%');
            }
            // $query->where('organization.organization_id', Helper::organId());
            $query->whereHas('organization', function (Builder $query) {
                $query->where('id', Helper::organId());
            });

            return $query;
        });
        if($this->filter->has('department')){
            $this->builder->whereHas('departments', function (Builder $query){
                if ($this->filter->has('department')) {
                    $query = $query->where(function ($subQ) {
                        $subQ->where('name', 'LIKE', '%' . $this->filter->get('department') . '%')
                            ->orWhere('name_kana', 'LIKE', '%' . $this->filter->get('department') . '%');
                    });
                }

                $query->whereHas('organization', function (Builder $query) {
                    $query->where('id', Helper::organId());
                });
                return $query;
            });
        }

        return $this->endFilter();
    }

    /**
     * Save data to database from request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Http\JsonResponse|\Illuminate\Support\Collection|mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        //User request store data
        $requester = $request->user();

        /**
         * Insert handle
         */
        //Get request id of user
        if (!$request->input('id')) {
            $this->authorize('create', $requester);
            $this->validateStoreUser($request);

            //User exited, attach it
            $user = $this->repository->byEmail($request->input('email'));
            if (is_null($user)) {
                return $this->insert($request);
            }

            return $this->attachUserToOrganization($user, $request);
        }

        /**
         * Update handle
         */
        return $this->update($request);
    }

    /**
     * Logged in user need load all relationships
     *
     * @param \App\Models\User $user
     * @param string $organId
     *
     * @return \App\Models\User
     */
    public function loadRelationships($user, $organId)
    {
        return $user->load(
            [
                'departments' => function ($q) use ($organId) {
                    $q->where('id', $organId);
                },
                'companies'   => function ($q) use ($organId) {
                    $q->where('id', $organId);
                },
            ]
        );
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function delete($id)
    {
        $requestOrganId = Helper::organId();

        $user = $this->repository->detail($id, $requestOrganId);

        $user->companies()->detach($user->companies->pluck('id')->toArray());
        $user->departments()->detach($user->departments->pluck('id')->toArray());
        $user->organizations()->detach($requestOrganId);

        UserDetachedOrganEvent::dispatch($user, $requestOrganId, auth()->user());

        return true;
    }

    /**
     * @param array $ids
     *
     * @return bool
     */
    public function deleteMultiple($ids)
    {
        foreach ($ids as $userId) {
            $this->delete($userId);
        }

        return true;
    }

    /**
     *
     * @param string $email
     *
     * @return true if delete success
     */
    public function checkEmail($email)
    {
        return $this->repository->byEmail($email);
    }

    /**
     * Export user information into csv file
     *
     * @param $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export($request)
    {
        $users              = $this->find($request->userIds, ['companies', 'departments']);
        $dataUsers          = [];
        foreach ($users as $user) {
            $dataUser = [
                'departments'   => implode(',', array_column($user['departments']->toArray(), 'name')),
                'name'          => $user->name,
                'company'       => implode(',', array_column($user['companies']->toArray(), 'name')),
                'prefecture'    => $this->appHelpers->getPrefectureName(implode(',', array_column($user['companies']->toArray(), 'prefecture'))),
                'city'          => implode(',', array_column($user['companies']->toArray(), 'city'))
            ];
            array_push($dataUsers, $dataUser);
        }

        $date = Carbon::now()->format('YmdHis');
        return Excel::download(new UserExport($dataUsers), 'users_' . $date . '.csv');
    }

    /**
     * Filter user department
     */
    public function userDepartmentFilter($request)
    {
        //set limit of list user
        $limit = $request->limit ? $request->limit : 10;
        //setup search of list user
        $userName = $request->name ? $request->name : '';
        $compName = $request->company ? $request->name : '';
        $comCity = $request->city ? $request->city : '';

        $where = [
            ['users.name', 'LIKE', '%' . $userName . '%'],
            ['companies.name', 'LIKE', '%' . $compName . '%'],
            ['companies.city', 'LIKE', '%' . $comCity . '%']
        ];

        if ($request->prefecture) {
            array_push($where, ['companies.prefecture', '=', $request->prefecture]);
        }

        //setup sort of list user
        $column = 'users.created_at';
        if ($request->sort = 'name') {
            $column =  'users.name';
        }
        $arrColumns = ['company', 'prefecture', 'city'];
        if (in_array($request->sort, $arrColumns)) {
            $column = 'companies.' . $request->sort;
        }

        //get list users
        $users = DB::table('belongs')
            ->select(
                'organizations.name as org_name',
                'departments.name as dep_name',
                'users.name as user_name',
                'companies.name as comp_name',
                'companies.prefecture as com_prefecture',
                'companies.city as com_city'
            )
            ->join('users', 'users.id', '=', 'belongs.user_id')
            ->join('departments', 'departments.id', '=', 'belongs.board_id')
            ->join('organizations', 'organizations.id', '=', 'departments.organization_id')
            ->join('user_companies', function ($userCom) {
                $userCom->on('user_companies.user_id', '=', 'users.id')
                    ->on('user_companies.organization_id', '=', 'departments.organization_id');
            })
            ->join('companies', function ($com) {
                $com->on('companies.id', '=', 'user_companies.company_id')
                    ->on('companies.organization_id', '=', 'departments.organization_id');
            })
            ->where($where)
            ->orderBy($column, 'DESC')
            ->paginate($limit);

        return $users;
    }

    /**
     * Check organization account creation limit
     *
     * @return bool
     */
    public function createAccountLimited()
    {
        $organAccess = Helper::organAccess();
        $accountLimit = $organAccess->get('account_limit');
        $organId = $organAccess->get('id');

        return $this->repository->getModel()->whereHas('organizations', function ($q) use ($organId) {
            $q->where('organization_id', $organId);
        })->count() >= $accountLimit;
    }

    /**
     * Insert user. If you want to validate use store() function
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Throwable
     */
    private function insert(Request $request)
    {
        $requestData = $request->except('avatar');
        $requestData['password'] = Hash::make($requestData['password']);
        if ($request->hasFile('avatar')) {
            $requestData['avatar'] = FileManager::path(FILE_TYPE_USER)->upload($request->avatar);
        }

        $user = $this->repository->create($requestData);

        $requestOrganId = Helper::organId();

        //Add company
        $user->companies()->attach($request->input('company_id'), ['organization_id' => $requestOrganId]);

        //Add department
        $user->departments()->wherePivot('organization_id', $requestOrganId)
            ->syncWithPivotValues($request->input('departments'), ['organization_id' => $requestOrganId]);

        //Add role and organization
        $user->organizations()->attach($request->input('organization_id'), $request->only(['role']));

        //Fire event
        UserCreatedEvent::dispatch($user, $request->input('organization_id'), $request->user()->id);

        return $user;
    }

    /**
     * Update user
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Throwable
     */
    private function update(Request $request)
    {
        $requester = $request->user();
        $user = $this->find($request->input('id'), ['organizations']);

        // Validate permission
        $this->authorize('update', [$requester, $user]);


        // User existed on system, handle attach to organization
        if (is_null($user->organizations->firstWhere('id', Helper::organId()))) {
            $this->validateStoreUser($request);
            return $this->attachUserToOrganization($user, $request);
        }

        //Normal user can update only avatar and password
        $requestData = $requester->isAdminHigher()
            ? $request->except(['avatar'])
            : $request->only(['password']) ;

        if (isset($requestData['password'])) {
            $requestData['password'] = Hash::make($requestData['password']);
        }

        //Upload avatar
        if ($request->hasFile('avatar')) {
            $requestData['avatar'] = FileManager::path(FILE_TYPE_USER)->upload($request->avatar);
        }

        //Delete avatar
        if (!$request->filled('old_avatar')) {
            FileManager::delete($user->avatar);
        }

        //Update data user company
        $user = $this->repository->updateByModel($user, $requestData);

        if (!$requester->isAdminHigher()) {
            return $user;
        }

        $requestOrganId = Helper::organId();

        //Update Role in Organization
        if ($request->filled('role')) {
            $user->organizations()->updateExistingPivot($requestOrganId, [
                'role' => $request->input('role'),
            ]);
        }

        //Update company in Organization
        $user->companies()->wherePivot('organization_id', $requestOrganId)
            ->syncWithPivotValues([$request->input('company_id')], ['organization_id' => $requestOrganId]);

        //Department
        $user->departments()->wherePivot('organization_id', $requestOrganId)
            ->syncWithPivotValues($request->input('departments'), ['organization_id' => $requestOrganId]);

        return $user;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     */
    private function validateStoreUser(Request $request)
    {
        $organization = $this->organRepository->find(Helper::organId());

        $createdUsers = $organization->users()->select('email')->get();

        //Validate limit create account of organization
        abort_if(
            $organization->account_limit <= $createdUsers->count(),
            422,
            trans('message.account_limit')
        );

        //Validate exist email available in organization
        abort_if(
            !is_null($createdUsers->firstWhere('email', $request->input('email'))),
            422,
            trans('message.account_existed')
        );

        return $organization;
    }

    /**
     * @param \App\Models\User $user
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Models\User
     */
    private function attachUserToOrganization($user, Request $request)
    {
        $organId = Helper::organId();
        $user->organizations()
            ->attach(
                $organId,
                ['role' => $request->input('role')]
            );
        $user->departments()->attach($request->input('departments'), ['organization_id' => $organId]);
        $user->companies()->attach($request->input('company_id'), ['organization_id' => $organId]);

        //Fire event
        OrganAttachedUserEvent::dispatch($user, $organId, $request->user());

        return $user;
    }

    // import user
    public function importUser($file)
    {
        $this->setInputEncoding($file);
        $organization = $this->organRepository->find(Helper::organId());
        $user = $this->repository;
        $userImport = new UsersImport($organization, $user);
        $userImport->import($file);
        $result = [
            'errors' => $userImport->errors(),
            'success' => $userImport->count,
        ];
        return $result;
    }

    private function setInputEncoding($file) {
        $fileContent = file_get_contents($file->path());
        $enc = mb_detect_encoding($fileContent, mb_list_encodings(), true);
        \Config::set('excel.imports.csv.input_encoding', $enc);
    }

}
