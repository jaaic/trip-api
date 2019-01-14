<?php

namespace App\Modules\Activities\Models;

use App\Exceptions\BadRequestException;
use Jenssegers\Mongodb\Eloquent\Model;

class Activity extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * Mongo collection
     *
     * @var string
     */
    protected $collection = 'berlin_activities';

    /** @var array */
    protected $fillable = ['_id',
                           'id',
                           'duration',
                           'price',
                           'city',
                           'country',
                           'created_at',
                           'updated_at'];

    /**
     * Get activities by $country and city
     *
     * @param string $city
     * @param string $country
     *
     * @return array
     * @throws \App\Exceptions\BadRequestException
     */
    public function getActivitiesByCityAndCountry(string $city, string $country): array
    {
        $activities = Activity::where('city', $city)
                              ->where('country', $country)
                              ->orderBy('price', 'asc')
                              ->get();


        if (empty($activities)) {
            throw new BadRequestException('No records found. Please change city and country!');
        }


        /** Illuminate\Database\Eloquent\Collection $activities */
        return $activities->toArray();
    }

    /**
     * Get Cheapest activities in city
     *
     * @param string $city
     * @param string $country
     *
     * @return float
     * @throws \App\Exceptions\BadRequestException
     */
    public function getCheapestActivityByCityAndCountry(string $city, string $country): float
    {
        $price = Activity::where('city', $city)
                         ->where('country', $country)
                         ->min('price');

        if (empty($price)) {
            throw new BadRequestException('No records found. Please change city and country!');
        }

        return $price;
    }


    /**
     * Helper to get avg price of activities per city
     *
     * @param string $city
     * @param string $country
     *
     * @return float
     * @throws \App\Exceptions\BadRequestException
     */
    public function getAvgActivityPriceByCityAndCountry(string $city, string $country): float
    {
        $price = Activity::where('city', $city)
                         ->where('country', $country)
                         ->avg('price');

        if (empty($price)) {
            throw new BadRequestException('No records found. Please change city and country!');
        }

        return $price;
    }

    /**
     * Helper to get avg price of activities per city
     *
     * @param string $city
     * @param string $country
     *
     * @return int
     * @throws \App\Exceptions\BadRequestException
     */
    public function getAvgActivityTimeByCityAndCountry(string $city, string $country): int
    {
        $duration = Activity::where('city', $city)
                            ->where('country', $country)
                            ->avg('duration');

        if (empty($duration)) {
            throw new BadRequestException('No records found. Please change city and country!');
        }

        return $duration;
    }
}