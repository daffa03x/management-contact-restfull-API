<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function register(UserRegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            if (User::where('username', $data['username'])->count() == 1) {
                throw new HttpResponseException(response([
                    "errors" => [
                        "username" => [
                            "username already registered"
                        ]
                    ]
                ], 400));
            }

            $user = new User($data);
            $user->password = Hash::make($data['password']);
            $user->save();
            return (new UserResource($user))->response()->setStatusCode(201);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }

    }
}
