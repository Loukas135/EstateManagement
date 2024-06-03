<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;



class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'fullname' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string|min:8',
            'profileImage' => 'image|mimes:jpg,png',
            'id_image' => 'image|mimes:jpg,png',
            'type' => 'required|string'
        ]);

        if(!$data)
        {
            return response()->json([
                'message' => 'invalid data'
            ], 400);
        }

        if(User::firstWhere('email', $data['email']
           || User::firstWhere('username', $data['username'])))
        {
            return response()->json([
                'message' => 'user already exists'
            ], 400);
        }

        $newUser = User::create([
            'fullname' => $data['fullname'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => $data['type']
        ]);

        //storing the seller id image if the user type is seller
        if($newUser->type == 'Seller'){
            $imageName = $request->fullname.'.'.$request->id_image->getClientOriginalExtension();
            Storage::disk('seller_id')->put($imageName, file_get_contents($request->id_image));
        }

        //storing the profile image if he uploads it
        if($request->profileImage){
            $imageName = $request->profileImage.'.'.$request->id_image->getClientOriginalExtension();
            Storage::disk('user_profile')->put($imageName, file_get_contents($request->profileImage));
        }

        if($newUser)
        {
            return response()->json([
                'message' => 'user has been created',
                'new user' => $newUser
            ], 201);
        }
    }

    public function login_as_seller(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if(!$credentials){
            return response()->json([
                'message' => 'Invalid data'
            ], 400);
        }

        $user = User::firstWhere('username', $credentials['username']);
        if(!$user || !Hash::check($request->password, $user->password))
        {
            return response()->json([
                'message' => 'Wrong credentials'
            ], 400);
        }

        $token = $user->createToken('API_TOKEN', ['role:seller']);

        return response()->json([
            'message' => 'logged in successfuly',
            'token' => $token->plainTextToken
        ], 200);
    }

    public function login_as_customer(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if(!$credentials){
            return response()->json([
                'message' => 'Invalid data'
            ], 400);
        }

        $user = User::firstWhere('email', $credentials['email']);
        
        if(!$user || !Hash::check($request->password, $user->password))
        {
            return response()->json([
                'message' => 'Wrong credentials'
            ], 400);
        }

        $token = $user->createToken('API_TOKEN', ['role:customer']);

        return response()->json([
            'message' => 'logged in successfuly',
            'access token' => $token->plainTextToken
        ], 200);
    }

    public function login_as_admin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if(!$credentials){
            return response()->json([
                'message' => 'Invalid data'
            ], 400);
        }

        $user = User::firstWhere('email', $credentials['email']);
        
        if(!$user || !Hash::check($request->password, $user->password))
        {
            return response()->json([
                'message' => 'Wrong credentials'
            ], 400);
        }

        $token = $user->createToken('API_TOKEN', ['role:admin']);

        return response()->json([
            'message' => 'logged in successfuly',
            'access token' => $token->plainTextToken
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'message' => 'you have been logged out'
        ], 204);
    }
}
