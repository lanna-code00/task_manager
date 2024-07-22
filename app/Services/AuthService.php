<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class AuthService {
    function __construct(User $user)
    {
        $this->user = $user;
    }

    function signUpUser(array $data)
    {
        try {
            $_user = $this->user->create($data);

            return response()->json([

                'status' => 'success',

                'message' => 'User created successfully',

                'token' => $_user->createToken('token_name')->plainTextToken

            ], 201);

        } catch (\Throwable $th) {

             return response()->json([

                'status' => 'error', 'message' => 'An unexpected error occurred.'

            ], 500);
        }
    }

    public function signInUser(array $data)
    {
        try {
            $validator = Validator::make($data, [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => 'error', 
                        
                        'message' => $validator->errors()->toArray()
                    ], 422
                );
            }

            if (!Auth::attempt($data)) {
                return response()->json(
                    [
                        'status' => 'error', 

                        'message' => 'Invalid credentials!'
                    ], 401
                );
            }

            $token = auth()->user()->createToken('token_name')->plainTextToken;

            return response()->json([

                'status' => 'success',

                'message' => 'Signed in successfully',

                'token' => $token

            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error', 'message' => 'An unexpected error occurred.'
            ], 500);
        }
    }

}