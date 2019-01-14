<?php

use Illuminate\Database\Seeder;
use App\Modules\Activities\Models\Activity;

class RemoveBerlinActivitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Activity::truncate();
    }
}
