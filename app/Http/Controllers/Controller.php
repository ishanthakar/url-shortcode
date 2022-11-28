<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Laravel Url shorting demo Documentation",
     *      description="Laravel Swagger Url shorting demo apis",
     *      @OA\Contact(
     *          email="admin@project.com"
     *      ),
     *      @OA\License(
     *          name="Apache 2.0",
     *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *      )
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Demo API Server"
     * )
     */
    
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * success response method.
     *
     * @param $message
     * @param $result
	 * @param $notify
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($message = "", $result, $notify = false)
    {
        $response = [
            'message' => $message,
            'data'    => $result,
        ];

        (!empty($notify)) && $response['notify'] = $message;
        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @param $error
     * @param array $errorMessages
     * @param int $code
	 * @param $notify
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, $errorMessages = [],  $code = 404, $notify = false)
    {
        $response = [
            'message' => $error,
        ];

        (!empty($notify)) && $response['notify'] = $error;
		if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }
        return response()->json($response, $code);
    }
}
