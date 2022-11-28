<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{

    /**
	 * @OA\Post(
	 ** path="/login",
	 *   tags={"Login"},
	 *   summary="Login User",
	 *   operationId="login",
	 *
	 *   @OA\Parameter(
	 *      name="email",
	 *      in="query",
	 *      required=true,
	 *      @OA\Schema(
	 *           type="string"
	 *      )
	 *   ),
	 *   @OA\Parameter(
	 *      name="password",
	 *      in="query",
	 *      required=true,
	 *      @OA\Schema(
	 *          type="string"
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *      @OA\MediaType(
	 *           mediaType="application/json",
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated"
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request"
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found"
	 *   ),
	 *   @OA\Response(
	 *      response=403,
	 *      description="Forbidden"
	 *   ),
	 *   @OA\Response(
	 *      response=500,
	 *      description="Server Error"
	 *   )
	 *)
	 **/
    protected function login(Request $request)
    {
        try{
            $postData = $request->all();
            $validator = \Validator::make($postData, [
                'email' => 'required|email',
                'password' => 'required|string',
            ], [
                'email.required' => "Please enter email!",
                'email.email' => "Please enter valid email!",
                'password.required' => "Please enter password!",
                'password.min'      => "Please enter valid password with min 8 characters!",
                'password.max'      => "Please enter valid password with max 20 characters!",
            
            ]);

            if ($validator->fails()) {
                return $this->sendError('Failed to login!', $validator->errors(), 400, true);
            }
            $credentials = $request->only('email', 'password');

            $token = auth('api')->attempt($credentials);
            if (!$token) {
                return $this->sendError('Unauthorized login!', ["general" => 'Unauthorized login!'], 401);
            }

            $user = auth('api')->user();
            $tokenType = 'bearer';
            $expiresIn = auth('api')->factory()->getTTL();
            return $this->sendResponse('Login Successful!', [
                compact('token',  'expiresIn')
            ], true);
        } catch (Exception $e) {
            return $this->sendError('Failed to login!', ['general' => $e->getMessage()], 500);
        }
    }

    /**
	 * @OA\Post(
	 ** path="/auth-refresh",
	 *   tags={"Login"},
	 *   summary="refresh token",
	 *   operationId="refreshtoken",
	 *
	 *   security={{"bearer_token":{}}},
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *      @OA\MediaType(
	 *           mediaType="application/json",
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated"
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request"
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found"
	 *   ),
	 *   @OA\Response(
	 *      response=403,
	 *      description="Forbidden"
	 *   ),
	 *   @OA\Response(
	 *      response=500,
	 *      description="Server Error"
	 *   )
	 *)
	 **/
    public function refresh()
    {
		try{
			$token =  auth('api')->refresh();
			$expiresIn = auth('api')->factory()->getTTL();
			return $this->sendResponse('Refresh Successful!', [
				compact('token',  'expiresIn')
			], true);
		} catch (Exception $e) {
			throw $e;
			return $this->sendError('Failed to refresh!', ['general' => $e->getMessage()], 500);
		}

    }

}
