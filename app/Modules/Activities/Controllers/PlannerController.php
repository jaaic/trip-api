<?php

namespace App\Modules\Activities\Controllers;

use App\Modules\Activities\Request\ActivityRequest;
use App\Modules\Activities\Services\FetchActivityService;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

/**
 * Class PlannerController
 *
 * @package App\Modules\Activities\Controllers
 * @author  Jaai Chandekar
 */
class PlannerController extends Controller
{
    /** @var \Illuminate\Http\Request */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Plan activities
     *
     * @throws \Exception
     */
    public function getActivities(): array
    {
        $request = new ActivityRequest();

        $response = $request->load($this->request->all())
                            ->validate()
                            ->validateBudgetPerDay()
                            ->process();

        return $response;
    }

}