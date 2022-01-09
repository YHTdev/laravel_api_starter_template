<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\AuthRepo;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(AuthRepo $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    public function login(Request $request)
    {
        $response = $this->authRepo->login($request);
        return  response()->json($response);
    }

    public function register(Request $request)
    {
        $response = $this->authRepo->register($request);
        return  response()->json($response);
    }

    public function resetPassword(Request $request)
    {
        $response = $this->authRepo->resetPassword($request);
        return  response()->json($response);
    }

    public function sendEmailPasswordLink(Request $request)
    {
        $response = $this->authRepo->sendEmailPasswordLink($request);
        return  response()->json($response);
    }

    public function user(Request $request)
    {
        $response = $this->authRepo->user($request);
        return  response()->json($response);
    }

    public function users(Request $request)
    {
        $response = $this->authRepo->users($request);
        return  response()->json($response);
    }

    public function updateUser(Request $request, $id)
    {
        $response = $this->authRepo->updateUser($request, $id);
        return  response()->json($response);
    }

    public function deleteUser($id)
    {
        $response = $this->authRepo->deleteUser($id);
        return  response()->json($response);
    }
}
