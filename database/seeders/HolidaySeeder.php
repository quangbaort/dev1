<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $organizationIds = \App\Models\Organization::pluck('id')->all();
        foreach($organizationIds as $orgId) {
            $this->insetHoliday($orgId);
        }
    }

    private function insetHoliday($orgId) {
        $holidays = $this->holiday2022($orgId);

        foreach($holidays as $holiday) {
            \App\Models\Holiday::create($holiday);
        }
    }

    private function holiday2022($orgId) {
        return [
            ['organization_id' => $orgId, 'date' => '2022/1/1', 'name' => '元日'],
            ['organization_id' => $orgId, 'date' => '2022/1/10', 'name' => '成人の日'],
            ['organization_id' => $orgId, 'date' => '2022/2/11', 'name' => '建国記念の日'],
            ['organization_id' => $orgId, 'date' => '2022/2/23', 'name' => '天皇誕生日'],
            ['organization_id' => $orgId, 'date' => '2022/3/21', 'name' => '春分の日'],
            ['organization_id' => $orgId, 'date' => '2022/4/29', 'name' => '昭和の日'],
            ['organization_id' => $orgId, 'date' => '2022/5/3', 'name' => '憲法記念日'],
            ['organization_id' => $orgId, 'date' => '2022/5/4', 'name' => 'みどりの日'],
            ['organization_id' => $orgId, 'date' => '2022/5/5', 'name' => 'こどもの日'],
            ['organization_id' => $orgId, 'date' => '2022/7/18', 'name' => '海の日'],
            ['organization_id' => $orgId, 'date' => '2022/8/11', 'name' => '山の日'],
            ['organization_id' => $orgId, 'date' => '2022/9/19', 'name' => '敬老の日'],
            ['organization_id' => $orgId, 'date' => '2022/9/23', 'name' => '秋分の日'],
            ['organization_id' => $orgId, 'date' => '2022/10/10', 'name' => 'スポーツの日'],
            ['organization_id' => $orgId, 'date' => '2022/11/3', 'name' => '文化の日'],
            ['organization_id' => $orgId, 'date' => '2022/11/23', 'name' => '勤労感謝の日'],
        ];
    }
}
