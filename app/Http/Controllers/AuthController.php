<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Fluent;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Register a new account
     */

    public function register(RegisterRequest $request){
        $data = $request->validated();

        $user = new User();
        $user->name     =   $data['name'];
        $user->email    =   $data['email'];
        $user->password =   Hash::make($data['password']);
        $user->save();

        // hide sensitive attributes from the response
        $user->makeHidden(['password', 'remember_token']);

        return response()->json([
            'code'   =>  Response::HTTP_CREATED,
            'status' =>  Response::$statusTexts[Response::HTTP_CREATED],
            'data'   =>  $user
        ], Response::HTTP_CREATED);
    }

    /**
     * Login & return auth token
     */

    public function login(LoginRequest $request){
        if (Auth::attempt($request->validated())) {

            $user        = Auth::user();
            $expires     = now()->addWeek();
            $newToken    = $user->createToken('access-token', ['*'], $expires);

            $plainTextToken = $newToken->plainTextToken;
            $tokenData = $newToken->accessToken;

            $token = new Fluent([
                'access_token'  =>  $plainTextToken,
                'token_type'    =>  'Bearer',
                'expired_at'    =>  $expires->timestamp,
                'expires_date'  =>  $expires->toDateTime(),
            ]);

            return response()->json([
                'code'      =>  Response::HTTP_OK,
                'status'    =>  Response::$statusTexts[Response::HTTP_OK],
                'data'      =>  $token,
            ], Response::HTTP_OK);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),

        ]);
    }

    /**
     * Get current user
     */
    public function user(Request $request) {
        $user = $request->user();

        return response()->json([
            'code'      =>  Response::HTTP_OK,
            'status'    =>  Response::$statusTexts[Response::HTTP_OK],
            'data'      =>  $user
        ], Response::HTTP_OK);
    }

    /**
     * Logout user and revoke tokens
     */

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'code'      =>  Response::HTTP_OK,
            'status'    =>  Response::$statusTexts[Response::HTTP_OK]
        ]);
    }
}
