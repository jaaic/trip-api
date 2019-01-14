<?php

use Codeception\Util\HttpCode;

/**
 * Class TripPlannerCest
 *
 * *********************
 * Run before tests
 *
 * php artisan db:seed --class=BerlinActivitiesSeeder
 *
 * **********************
 *
 * @author Jaai Chandekar
 */
class TripPlannerCest
{
    /** @var string */
    protected $path;

    /**
     * Setup before each test
     *
     */
    public function _before()
    {
        $this->path = '/planner';
    }

    /**
     * Test successful call
     *
     * @param \ApiTester $I
     */
    public function testPlanner(ApiTester $I)
    {
        $requestParams = [
            'budget' => 680,
            'days'   => 2,
        ];

        /** @var \ApiTester $I api tester */
        $I->sendGET($this->path, $requestParams);
        $I->seeResponseCodeIs(HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $response = json_decode($I->grabResponse(), true);

        if (!empty($response)) {
            $isValid = $this->checkResponseAttributes($response);
        } else {
            $isValid = true;
        }

        \PHPUnit_Framework_Assert::assertTrue($isValid);
    }

    /**
     * Test elements in response
     *
     * @param array $searchResponse
     *
     * @return bool
     */
    private function checkResponseAttributes(array $searchResponse)
    {

        $mainKey = array_keys($searchResponse);
        if (!in_array('schedule', $mainKey)) {
            return false;
        }

        $secondLevelKeys = array_keys($searchResponse['schedule'] ?? []);
        if (!in_array('days', $secondLevelKeys) ||
            !in_array('summary', $secondLevelKeys)) {
            return false;
        }

        return true;
    }

    /**
     * Test invalid inputs call
     *
     * @param \ApiTester $I
     */
    public function testInvalidDays(ApiTester $I)
    {
        $requestParams = [
            'budget' => 680,
            'days'   => 10,
        ];

        /** @var \ApiTester $I api tester */
        $I->sendGET($this->path, $requestParams);
        $I->seeResponseIsJson();
        $response = json_decode($I->grabResponse(), true);

        \PHPUnit_Framework_Assert::assertEquals(400, $response['status']);
    }

    /**
     * Test invalid budget
     *
     * @param \ApiTester $I
     */
    public function testInvalidBudgetDays(ApiTester $I)
    {
        $requestParams = [
            'budget' => 1,
            'days'   => 1,
        ];

        /** @var \ApiTester $I api tester */
        $I->sendGET($this->path, $requestParams);
        $I->seeResponseIsJson();
        $response = json_decode($I->grabResponse(), true);

        \PHPUnit_Framework_Assert::assertEquals(400, $response['status']);
    }

    /**
     * Test invalid budget
     *
     * @param \ApiTester $I
     */
    public function testDifferentCity(ApiTester $I)
    {
        $requestParams = [
            'budget' => 1,
            'days'   => 1,
            'city'   => 'Dubai',
        ];

        /** @var \ApiTester $I api tester */
        $I->sendGET($this->path, $requestParams);
        $I->seeResponseIsJson();
        $response = json_decode($I->grabResponse(), true);

        \PHPUnit_Framework_Assert::assertEquals(400, $response['status']);
    }
}