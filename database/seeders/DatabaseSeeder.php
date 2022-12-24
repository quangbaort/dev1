<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (User::count() == 0) {
            $this->call([UserAdminDefaultSeeder::class]);

            $this->command->info('seeding Organization ...');

            \App\Models\Organization::factory(1)->create();
        }

        if (in_array(app()->environment(), ['local', 'dev', 'test'])) {
            $this->createDataForTest();
        }
    }

    private function createDataForTest()
    {
        $DATA_ROW = 100;
        if (!env('USE_FAKE_DATA', false)) {
            return;
        }

        $this->command->info('seeding Organization ...');
        \App\Models\Organization::factory(1)->create();

        $this->command->info('seeding Company ...');
        \App\Models\Company::factory($DATA_ROW)->create();

        $this->command->info('seeding User ...');
        \App\Models\User::factory($DATA_ROW)->create();

        $this->command->info('seeding UserRole ...');
        \App\Models\UserRole::factory($DATA_ROW)->create();

        $this->command->info('seeding Department ...');
        \App\Models\Department::factory($DATA_ROW)->create();

        $this->command->info('seeding Holiday ...');
        \App\Models\Holiday::factory(10)->create();

        $this->command->info('seeding Memo ...');
        \App\Models\Memo::factory($DATA_ROW)->create();

        $this->command->info('seeding Log ...');
        \App\Models\Log::factory($DATA_ROW)->create();

        $this->command->info('seeding UserRole ...');
        \App\Models\UserRole::factory(10)->create();

        $this->command->info('seeding News ...');
        \App\Models\News::factory(30)->create();

        $this->command->info('seeding NewsReply ...');
        for ($i = 0; $i < 50; $i++) {
            \App\Models\NewsResponse::factory(1)->create();
        }

        $this->command->info('seeding Belong ...');
        \App\Models\UserDepartment::factory(20)->create();

        $this->command->info('seeding NotifyGroup ...');
        \App\Models\NotifyRecipientGroup::factory($DATA_ROW)->create();

        // Can phai chay tung lenh vi co query den chinh bang dang thao tac
        $this->command->info('seeding NotifyUser ...');
        for ($i = 0; $i < 10; $i++) {
            \App\Models\NotifyRecipient::factory(1)->create();
        }

        $this->command->info('seeding Belong ...');
        for ($i = 0; $i < 50; $i++) {
            \App\Models\UserDepartment::factory(1)->create();
        }

        $this->command->info('seeding UserCompany ...');
        for ($i = 0; $i < 50; $i++) {
            \App\Models\UserCompany::factory(1)->create();
        }

        $this->command->info('seeding Events ...');
        \App\Models\Event::factory(10)->create();
        \App\Models\EventInviteResponse::factory(10)->create();
    }
}
