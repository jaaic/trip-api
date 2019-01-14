<?php

namespace App\Modules\Activities\Request;

use App\Core\Base\Request;
use App\Core\Constants;
use App\Exceptions\BadRequestException;
use App\Modules\Activities\Services\FetchActivityService;
use App\Modules\Activities\Response\ErrorResponse;
use Illuminate\Support\Facades\Log;

/**
 * Class ActivityRequest
 *
 * @property integer budget  Activity budget
 * @property integer days    Total days
 * @property string  city    Activity city
 * @property string  country Activity country
 *
 *
 * @package App\Modules\Activities\Request
 * @author  Jaai Chandekar
 */
class ActivityRequest extends Request
{
    /**
     * Request attributes
     *
     * @return array
     */
    function attributes(): array
    {
        return [
            'budget',
            'days',
            'city',
            'country',
        ];
    }

    /**
     * Request attribute validation rules.
     *
     * @return array
     */
    function rules(): array
    {
        return [
            'budget' => 'required|integer|between:100,5000',
            'days'   => 'required|integer|between:1,5',
        ];
    }

    /**
     * Check per day budget
     *
     * @return $this
     */
    public function validateBudgetPerDay()
    {
        // check validation errors
        if (empty($this->getErrors())) {

            $budget    = $this->getAttribute('budget');
            $totalDays = $this->getAttribute('days');

            // check if min budget per day >= 50
            if (($budget / $totalDays) < Constants::MIN_BUDGET_PER_DAY) {
                $exception = new BadRequestException('Min. budget per day should be >= ' . Constants::MIN_BUDGET_PER_DAY);

                $this->setErrors($exception->toArray());
            }
        }

        return $this;
    }

    /**
     * Process request
     *
     * @return array
     */
    public function process(): array
    {
        // check validation errors
        if (!empty($this->getErrors())) {
            $errors = $this->getErrors();
            Log::error(json_encode($errors));

            return (new ErrorResponse($errors))->transform();
        }

        try {
            $response = (new FetchActivityService($this->getAttributes()))->getActivities();
        } catch (BadRequestException $exception) {
            return (new ErrorResponse([
                'status' => $exception->getStatus(),
                'detail' => $exception->getMessage(),
            ]))->transform();
        }

        return $response;
    }

}