<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
//use Your Model

/**
 * Class AuthRepo.
 */
class AuthRepo extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return User::class;
    }

    public function login($request)
    {
        $returnObj = array();
        $returnObj['statusCode'] = 500;
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);
            if ($validator->fails()) {
                $returnObj['statusCode'] = 422;
                $returnObj['errors'] = $validator->errors();
            } else {
                $user = User::where('email', $request->email)->first();
                if ($user) {
                    if (Hash::check($request->password, $user->password)) {
                        $accessToken = $user->createToken('accessToken')->accessToken;
                        $returnObj['user'] = $user;
                        $returnObj['accessToken'] = $accessToken;
                        $returnObj['statusCode'] = 200;
                    } else {
                        $returnObj['statusCode'] = 422;
                        $returnObj['message'] = `User password doesn't match!`;
                    }
                } else {
                    $returnObj['statusCode'] = 422;
                    $returnObj['message'] = `User Email doesn't exist!`;
                }
            }
        } catch (\Throwable $th) {
            $returnObj['statusCode'] = 500;
            $returnObj['message'] = $th->getMessage();
        }
        return $returnObj;
    }

    public function register($request)
    {
        $returnObj = array();
        $returnObj['statusCode'] = 500;
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|unique:users,email',
                'password' => 'required|min:8'
            ]);
            if ($validator->fails()) {
                $returnObj['statusCode'] = 422;
                $returnObj['errors'] = $validator->errors();
            } else {
                $data = [
                    'password' => Hash::make($request->password),
                    'email' => $request->email,
                    'name' => $request->name
                ];
                $user = User::create($data);
                $accessToken = $user->createToken('accessToken')->accessToken;
                $returnObj['accessToken'] = $accessToken;
                $returnObj['user'] = $user;
                $returnObj['statusCode'] = 201;
            }
        } catch (\Throwable $th) {
            $returnObj['statusCode'] = 500;
            $returnObj['message'] = $th->getMessage();
        }
        return $returnObj;
    }

    public function logout($request)
    {
        $returnObj = array();
        $returnObj['statusCode'] = 500;
        try {
            $request->user()->token()->revoke();
            $returnObj['statusCode'] = 200;
            $returnObj['message'] = 'Logout successfully';
        } catch (\Throwable $th) {
            $returnObj['statusCode'] = 500;
            $returnObj['message'] = $th->getMessage();
        }
        return $returnObj;
    }

    public function resetPassword($request)
    {
        $returnObj = array();
        $returnObj['statusCode'] = 500;

        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|min:8|confirmed',

            ]);
            if ($validator->fails()) {
                $returnObj['statusCode'] = 422;
                $returnObj['errors'] = $validator->errors();
            } else {
                $user =  $request->user();
                $user->password = $request->password;
                $result = $user->save();
                $returnObj['statusCode'] = 200;
                $returnObj['message'] = 'Password reset successfully!';
                $returnObj['user'] = $result;
            }
        } catch (\Throwable $th) {
            $returnObj['statusCode'] = 500;
            $returnObj['message'] = $th->getMessage();
        }
        return $returnObj;
    }

    public function sendEmailPasswordLink($request)
    {
        $returnObj = array();
        $returnObj['statusCode'] = 500;
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);
            if ($validator->fails()) {
                $returnObj['statusCode'] = 422;
                $returnObj['errors'] = $validator->errors();
            } else {
                $response = Password::sendResetLink($request->email);
                $message = $response === Password::RESET_LINK_SENT ? 'Email sent successfully!' : 'something wrong!';
                $returnObj['statusCode'] = 200;
                $returnObj['message'] = $message;
            }
        } catch (\Throwable $th) {
            $returnObj['statusCode'] = 500;
            $returnObj['message'] = $th->getMessage();
        }
        return $returnObj;
    }



    public function user($request)
    {
        $returnObj = array();
        $returnObj['statusCode'] = 500;
        try {
        } catch (\Throwable $th) {
            $returnObj['statusCode'] = 500;
            $returnObj['message'] = $th->getMessage();
        }
        return $returnObj;
    }

    public function users($request)
    {
        $returnObj = array();
        $returnObj['statusCode'] = 500;
        try {
        } catch (\Throwable $th) {
            $returnObj['statusCode'] = 500;
            $returnObj['message'] = $th->getMessage();
        }
        return $returnObj;
    }

    public function updateUser($request, $id)
    {
        $returnObj = array();
        $returnObj['statusCode'] = 500;
        try {
        } catch (\Throwable $th) {
            $returnObj['statusCode'] = 500;
            $returnObj['message'] = $th->getMessage();
        }
        return $returnObj;
    }

    public function deleteUser($id)
    {
        $returnObj = array();
        $returnObj['statusCode'] = 500;
        try {
        } catch (\Throwable $th) {
            $returnObj['statusCode'] = 500;
            $returnObj['message'] = $th->getMessage();
        }
        return $returnObj;
    }
}
