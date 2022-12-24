<?php


namespace App\Services;

use App\Services\Concerns\BaseService;
use App\Repositories\UserRoleRepository;
use App\Repositories\OrganizationRepository;

class MenuService extends BaseService
{
    public function __construct(UserRoleRepository $repository, OrganizationRepository $organizationRepository)
    {
        $this->repository = $repository;
        $this->organization = $organizationRepository;
    }
    public function index($request)
    {
        $menu = [];
        if ($request->user()->is_super_admin) {
            $menu = [
                ['function' => 'settings', 'type' => 'divider'],
                ['function' => 'Board', 'type' => 'link'],
                ['function' => 'Company', 'type' => 'link'],
                ['function' => 'User', 'type' => 'link'],
                ['function' => 'MailingList', 'type' => 'link'],
                ['function' => 'Holiday', 'type' => 'link'],
                ['function' => 'Organization', 'type' => 'link'],
                ['function' => 'other', 'type' => 'link'],
                ['function' => 'Memo', 'type' => 'link'],
                ['function' => 'Logs', 'type' => 'link']
            ];
        }
        if($request->organization_id){
            $function = $this->organization->where('id', $request->organization_id)->first();
            if ($function->calendar_enabled) {
                array_push($menu, ['function' => 'Calendar',  'type' => 'link']);
            }
            if ($function->news_enabled) {
                array_push($menu, ['function' => 'Notify', 'type' => 'link']);
            }
            if ($function->safety_check_enabled) {
                array_push($menu, ['function' => 'Safety', 'type' => 'link']);
            }
            if ($function->library_enabled) {
                array_push($menu, ['function' => 'Folder', 'type' => 'link']);
            }
            $userRole = $this->repository->where(['user_id' => $request->user()->id, 'organization_id' => $request->organization_id])->first();
            if (!is_null($userRole)) {
                if ($userRole->role == ADMIN_ROLE) {
                    $menu1 = [
                        ['function' => 'settings', 'type' => 'divider'],
                        ['function' => 'Board', 'type' => 'link'],
                        ['function' => 'Company', 'type' => 'link'],
                        ['function' => 'User', 'type' => 'link'],
                        ['function' => 'MailingList', 'type' => 'link'],
                        ['function' => 'Holiday', 'type' => 'link'],
                        ['function' => 'Memo', 'type' => 'link'],
                    ];
                    $menu = array_merge($menu, $menu1);
                }
            }
        }
        return $menu;
    }
}
