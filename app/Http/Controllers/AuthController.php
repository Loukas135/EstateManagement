<?php

namespace App\Http\Controllers;

use App\Models\Extra;
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
            'email' => 'required|string',
            'password' => 'required|string|min:8',
            'profileImage' => 'image',
            'id_image' => 'image',
            'type' => 'required|string'
        ]);

        if (!$data) {
            return response()->json([
                'message' => 'invalid data'
            ], 400);
        }

        if (User::firstWhere('email', $data['email'])) {
            return response()->json([
                'message' => 'user already exists'
            ], 400);
        }

        $profileImagePath = null;
        $idImagePath = null;


        if ($request->hasFile('id_image') && $data['type'] == 'Seller') {
            $idImageName = $request->file('id_image')->hashName();
            Storage::disk('seller_id')->put($idImageName, file_get_contents($request->file('id_image')));
            $idImagePath = Storage::disk('seller_id')->url($idImageName);
        }

        if ($request->hasFile('profileImage')) {
            $profileImageName = $request->file('profileImage')->hashName();
            Storage::disk('user_profile')->put($profileImageName, file_get_contents($request->file('profileImage')));
            $profileImagePath = Storage::disk('user_profile')->url($profileImageName);
        }

        $newUser = User::create([
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'profile_image' => $profileImagePath,
            'id_image' => $idImagePath,
            'type' => $data['type']
        ]);

        if ($newUser) {
            return response()->json([
                'message' => 'user has been created',
                'user' => $newUser
            ], 201);
        }
    }

    public function register_service(Request $request)
    {
        $data = $request->validate([
            'fullname' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string|min:8',
            'profileImage' => 'image',
            'type' => 'required|string',

            'category_id' => 'required',
            'name' => 'required|string',
            'contact_number' => 'required|string',
            'address' => 'required|string',
            'description' => 'required|string',
            'image'  => 'image',

        ]);


        if ($data) {

            if ($request->hasFile('profileImage')) {
                $profileImageName = $request->file('profileImage')->hashName();
                Storage::disk('user_profile')->put($profileImageName, file_get_contents($request->file('profileImage')));
                $data['profileImage'] = Storage::disk('user_profile')->url($profileImageName);
            }

            $newUser = User::create([
                'fullname' => $data['fullname'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'profile_image' => $data['profileImage'],
                'type' => $data['type']
            ]);

            if ($request->hasFile('image')) {
                $imageName = $request->image->hashName();
                Storage::disk('extra_images')->put($imageName, file_get_contents($request->image));
                $data['image'] = Storage::disk('extra_images')->url($imageName);
            }
            $extra = Extra::create([
                'name' => $data['name'],
                'category_id' => $data['category_id'],
                'user_id' => $newUser->id,
                "contact_number" => $data["contact_number"],
                'address' => $data['address'],
                'description' => $data['description'],
                'image' => $data['image'],
            ]);

            if ($extra) {
                return response()->json([
                    'message' => 'Added successfuly',
                    'data' => $extra
                ], 200);
            }
        }
        return response()->json([
            'message' => 'wrong_data'
        ], 400);
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if (!$credentials) {
            return response()->json([
                'message' => 'Invalid data'
            ], 400);
        }

        $user = User::firstWhere('email', $credentials['email']);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Wrong credentials'
            ], 400);
        }

        $userType = strtolower($user->type);
        $token = $user->createToken('API_TOKEN', ["role:$userType"]);

        return response()->json([
            'message' => 'logged in successfuly',
            'user' => $user,
            'token' => $token->plainTextToken
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
