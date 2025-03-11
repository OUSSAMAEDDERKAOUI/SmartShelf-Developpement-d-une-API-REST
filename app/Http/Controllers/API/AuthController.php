<?php

namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use function Laravel\Prompts\password;

class AuthController extends Controller
{

    public function register(Request $request)
{
    // Validation des données
    $ValidateData = $request->validate([
        "username" => "required|max:100",
        "email" => "required|email|unique:users,email",  
        "password" => "required|min:8",
        "role" => "in:admin,client",  
    ]);

    $role = $ValidateData['role'] ?? 'client';

    $user = User::create([
        "username" => $ValidateData['username'],
        "email" => $ValidateData['email'],
        "password" => bcrypt($ValidateData['password']),
        "role" => $role,  
    ]);

    $token = $user->createToken('my-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'type' => 'Bearer',  
    ], 201);  
}


public function login(Request $request){


    $ValidateData=$request->validate([
        "username"=>"required|max:100",
        "password"=>"required",
    ]);
    $user=User::where('username',$ValidateData["username"])->first();

    if(!$user||!Hash::check($ValidateData['password'],$user->password)){
        return response([
            'message' => 'Wrong credentials'
        ]);
    }
    $token=$user->createToken('my_token')->plainTextToken;

    return response()->json([
        "token"=>$token,
        "type"=>"Bearer",
        "role"=>$user->role
    ]);
}




    /**
     * Display a listing of the resource.
     */
    public function index()
    {


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
