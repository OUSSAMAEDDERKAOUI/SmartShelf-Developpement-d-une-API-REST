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


    public function login(Request $request)
    {


        $ValidateData = $request->validate([
            "username" => "required|max:100",
            "password" => "required",

        ]);
        $user = User::where('username', $ValidateData["username"])->first();

        if (!$user || !Hash::check($ValidateData['password'], $user->password)) {
            return response([
                'message' => 'Wrong credentials'
            ]);
        }
        $token = $user->createToken('my_token')->plainTextToken;

        return response()->json([
            "token" => $token,
            "type" => "Bearer",
            "role" => $user->role
        ]);
    }



    public function logout(Request $request)
    {
        // Revoke the user's token
        $request->user()->tokens->each(function ($token) {
            $token->delete();  // Delete the token, effectively logging the user out
        });

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }


    //     public function store(Request $request)
    //     {
    // $validateData=$request->validate([
    //     'name'=>'required|max:100',
    //     'email'=>'required|email|unique:users',
    //     'password'=>'required|min:8',
    // ]);
    // $user= User::create([
    //     'name'=>$validateData['name'],
    //     'email'=>$validateData['email'],
    //     'password'=>bcrypt($validateData['password']),
    // ]);

    // return response()->json($user,201);

    //     }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validation des champs fournis dans la requête
        $validateData = $request->validate([
            'username' => 'required|string|max:100',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            "role" => "in:admin,client",

        ]);
        $role = $ValidateData['role'] ?? 'client';
        $user->role = $role;


        if ($request->has('username')) {
            $user->username = $validateData['username'];
        }

        if ($request->has('email')) {
            $user->email = $validateData['email'];
        }

        if ($request->has('password')) {
            $user->password = bcrypt($validateData['password']);
        }

        $user->save();

        return response()->json($user);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json("User account deleted");
 
   }
   
}
