<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


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
                        $returnObj['message'] = 'User password does not match!';
                    }
                } else {
                    $returnObj['statusCode'] = 422;
                    $returnObj['message'] = 'User Email does not exist!';
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
                'password_confirmation' => 'required',
                'token' => 'required',
                'email' => 'required|email|exists:users,email'

            ]);
            if ($validator->fails()) {
                $returnObj['statusCode'] = 422;
                $returnObj['errors'] = $validator->errors();
            } else {

                $updatePassword = DB::table('password_resets')->where([
                    'email' => $request->email,
                    'token' => $request->token
                ]);

                if ($updatePassword) {
                    $user = User::where('email', $request->email)->update([
                        'password' => Hash::make($request->password)
                    ]);
                    $returnObj['statusCode'] = 200;
                    $returnObj['message'] = 'Your password updated successfully';
                    $returnObj['user'] = $user;
                } else {
                    $returnObj['statusCode'] = 422;
                    $returnObj['message'] = 'Invalid token';
                }
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
                'email' => 'required|email|exists:users,email'
            ]);
            if ($validator->fails()) {
                $returnObj['statusCode'] = 422;
                $returnObj['errors'] = $validator->errors();
            } else {
                $token = Str::random(6);
                DB::table('password_resets')->insert([
                    'email' => $request->email,

                    'token' => $token,

                    'created_at' => Carbon::now()
                ]);
                Mail::send('email.forgetPassword', ['token' => $token], function ($message) use ($request) {
                    $message->to($request->email);
                    $message->subject('Reset Password');
                });
                if (Mail::failures()) {
                    $returnObj['message'] = 'Fail';
                    $returnObj['statusCode'] = 422;
                } else {
                    $returnObj['message'] = 'Success';
                    $returnObj['statusCode'] = 200;
                }
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
            $returnObj['user'] = $request->user();
            $returnObj['statusCode'] = 200;
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
            $users = User::orderBy('updated_at', 'desc')->paginate($request->limit ?? 10);
            $returnObj['users'] = $users;
            $returnObj['statusCode'] = 200;
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
            $user = User::findOrFail($id);
            if ($user) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required'
                ]);
                if ($validator->fails()) {
                    $returnObj['statusCode'] = 422;
                    $returnObj['errors'] = $validator->errors();
                } else {
                    $user->name = $request->name;
                    $user->save();
                    $returnObj['statusCode'] = 200;
                    $returnObj['message'] = "Updated successfully";
                }
            } else {
                $returnObj['statusCode'] = 422;
                $returnObj['message'] = 'User does not exist!';
            }
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
            $user = User::findOrFail($id);
            if ($user) {
                $user->delete();
                $returnObj['message'] = 'User deleted successfully!';
                $returnObj['statusCode'] = 200;
            } else {
                $returnObj['message'] = 'User does not exist!';
                $returnObj['statusCode'] = 422;
            }
        } catch (\Throwable $th) {
            $returnObj['statusCode'] = 500;
            $returnObj['message'] = $th->getMessage();
        }
        return $returnObj;
    }
}
