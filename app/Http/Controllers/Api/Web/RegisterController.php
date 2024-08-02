<?php

namespace App\Http\Controllers\Api\web;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;


class RegisterController extends Controller
{
  public function register(Request $request){
  
    // buat user register
      $user = User::create($request->only('name', 'email') + [
        'password' => Hash::make($request->password),
        'role' => 'users'
    ]);
    return response()->json([
        'succes' => true,
        'message' => 'succes',
        'data' => $user
    ], 200);

  }
}