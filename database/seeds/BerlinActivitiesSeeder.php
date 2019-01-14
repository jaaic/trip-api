<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Core\Constants;
use App\Modules\Activities\Models\Activity;

/**
 * Class BerlinActivitiesSeeder
 *
 * @author Jaai Chandekar <jaai.chandekar@tajawal.com>
 */
class BerlinActivitiesSeeder extends Seeder
{

    /** @var string */
    protected $activitiesDir = __DIR__ . '/../seeds/data/activities/';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $this->getActivities();
        } catch (Exception $exception) {
            Log::error('Error seeding activities -' . $exception->getMessage());
        }


    }

    /**
     * Read all activity files from the directory
     *
     * @return void
     *
     * @throws \Exception
     */
    public function getActivities(): void
    {
        if (is_dir($this->activitiesDir)) {
            if ($dh = opendir($this->activitiesDir)) {
                while (($file = readdir($dh)) !== false) {
                    // ignore links to current and parent dirs
                    if (($file != '.') && ($file != '..')) {
                        echo "Reading activities file:" . $file . PHP_EOL;

                        $fileJson = file_get_contents($this->activitiesDir . $file);

                        $dataArray = json_decode($fileJson, true);

                        if (!empty(json_last_error()) || empty($dataArray)) {
                            throw new Exception('Invalid Activities file : ' . $this->activitiesDir . $file);
                        }

                        //$activities = array_merge($activities, $dataArray);

                        $fileParts = explode('_', $file);
                        $city      = $fileParts[0] ?? Constants::DEFAULT_CITY;

                        // could be fetched from file name if the filename structure
                        // is like <country>_<city>_<number>.json
                        $country = Constants::DEFAULT_COUNTRY;

                        foreach ($dataArray as $activity) {
                            $activity['city']    = $city;
                            $activity['country'] = $country;
                            Activity::create($activity);
                        }
                    }
                }
            }
            closedir($dh);
        }
    }
}
