<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserAdminDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
          'name' => '管理社-System Administrator',
          'email' => 'admin-netmaster-dev@gmail.com',
          'password' => Hash::make('123456a@'),
          'is_super_admin' => true,
        ]);

        if (!env('USE_FAKE_DATA', false)) {
            return;
        }

        User::create([
            'name' => 'Administrator',
            'email' => 'admin@vaixgroup.com',
            'password' => Hash::make('123456a@'),
            'is_super_admin' => true,
        ]);

        $orgAdminEmail = 'org-admin@sample.com';
        User::create([
            'name' => '契約管理者-Organization Admin',
            'email' => $orgAdminEmail,
            'password' => Hash::make('123456a@'),
            'is_super_admin' => false,
        ]);
        $userEmail = 'user@sample.com';
        User::create([
          'name' => '一般ユーザー-Normal user',
          'email' => $userEmail,
          'password' => Hash::make('123456a@'),
          'is_super_admin' => false,
        ]);
        User::create([
            'name' => 'quangBao',
            'email' => 'quangbaorp@gmail.com',
            'password' => Hash::make('baooibao1'),
            'is_super_admin' => false
        ]);
        User::create([
            'name' => 'Yen Cung',
            'email' => 'cungyenhr@gmail.com',
            'password' => Hash::make('YenCung@123'),
            'is_super_admin' => true
        ]);


        $orgId = '1ec99137-5870-6b9e-8f21-92001a9e634c';
        $companyId1 = '1ec99178-b7b0-6258-bef4-92001a9e634c';
        $companyId2 = '1ec99177-b993-662a-b6ae-92001a9e634c';

        $this->createTestOrganization($orgId);
        $this->createTestCompany($orgId, $companyId1, $companyId2);
        \App\Models\Department::factory(3)->create();

        $orgAdminId = User::where('email', $orgAdminEmail)->pluck('id')->first();
        $userId = User::where('email', $userEmail)->pluck('id')->first();
        $boardId = \App\Models\Department::pluck('id')->first();

        DB::table('user_roles')->insert([
            'user_id' => $orgAdminId,
            'organization_id' => $orgId,
            'role' => ADMIN_ROLE
        ]);
        DB::table('user_roles')->insert([
            'user_id' => $userId,
            'organization_id' => $orgId,
            'role' => USER_ROLE
        ]);
        DB::table('belongs')->insert([
            'user_id' => $orgAdminId,
            'board_id' => $boardId,
            'organization_id' => $orgId
        ]);
        DB::table('belongs')->insert([
            'user_id' => $userId,
            'board_id' => $boardId,
            'organization_id' => $orgId
        ]);
        DB::table('user_companies')->insert([
            'user_id' => $orgAdminId,
            'company_id' => $companyId1,
            'organization_id' => $orgId,
        ]);
        DB::table('user_companies')->insert([
            'user_id' => $userId,
            'company_id' => $companyId2,
            'organization_id' => $orgId,
        ]);
    }

    private function createTestOrganization($orgId)
    {
        \App\Models\Organization::factory(1)->create();

        $org = \App\Models\Organization::first();
        $newOrg = $org->replicate();
        $newOrg->id = $orgId;
        $newOrg->name = 'Hiep hoi phan mem VN';
        $newOrg->push();

        $org->forceDelete();
    }

    private function createTestCompany($orgId, $companyId1, $companyId2)
    {
        \App\Models\Company::factory(1)->create();

        $company = \App\Models\Company::first();
        $newCompany = $company->replicate();
        $newCompany->organization_id = $orgId;
        $newCompany->id = $companyId1;
        $newCompany->name = 'VAIX VN';
        $newCompany->push();

        $secondCompany = $company->replicate();
        $secondCompany->organization_id = $orgId;
        $newCompany->id = $companyId2;
        $secondCompany->name = 'TNHH Ba con vit';
        $secondCompany->push();

        $company->forceDelete();
    }
}
