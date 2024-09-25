<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    public function getAllManagers()
    {
        $managers = User::where('type', 'Manager')->get();

        return response()->json([
            'managers' => $managers,
        ], 200);
    }

    public function updateManager(Request $request, $id)
    {
        $manager = User::where('type', 'Manager')->findOrFail($id);

        $validatedData = $request->validate([
            'fullname' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
        ]);

        if (isset($validatedData['fullname'])) {
            $manager->fullname = $validatedData['fullname'];
        }

        if (isset($validatedData['email'])) {
            $manager->email = $validatedData['email'];
        }

        if (isset($validatedData['password'])) {
            $manager->password = Hash::make($validatedData['password']);
        }


        $manager->save();

        return response()->json([
            'message' => 'Manager updated successfully',
            'manager' => $manager,
        ], 200);
    }

    public function deleteManager($id)
    {
        $manager = User::where('type', 'Manager')->findOrFail($id);

        $manager->delete();

        return response()->json([
            'message' => 'Manager deleted successfully',
        ], 200);
    }

    public function addManager(Request $request)
    {
        $validatedData = $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',

        ]);


        $manager = User::create([
            'fullname' => $validatedData['fullname'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'type' => 'Manager',
        ]);

        return response()->json([
            'message' => 'Manager created successfully',
            'manager' => $manager,
        ], 201);
    }
}
