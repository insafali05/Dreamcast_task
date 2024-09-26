<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function create()
    {
        $roles = Role::all();
        $users = User::with('role')->orderBy('created_at', 'desc')->get();

        if (request()->ajax()) {
            return response()->json(['users' => $users]);
        }

        return view('users.create', compact('roles', 'users'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|regex:/^[6-9][0-9]{9}$/',
            'description' => 'required|string|max:500',
            'role_id' => 'required|exists:roles,id',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'This email address is already taken.',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Please enter a valid 10-digit Indian phone number.',
            'description.required' => 'Description is required.',
            'role_id.required' => 'Please select a role.',
            'profile_image.required' => 'Profile image is required.',
            'profile_image.mimes' => 'The profile image must be a file of type: jpeg, png, jpg, gif.',
            'profile_image.max' => 'The profile image may not be greater than 2MB.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        try {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
        } catch (\Exception $e) {
            return response()->json(['errors' => ['profile_image' => 'Failed to upload profile image.']], 500);
        }


        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'description' => $request->description,
                'role_id' => $request->role_id,
                'profile_image' => $imagePath,
            ]);

            $user->load('role');
            DB::commit();

            return response()->json(['user' => $user], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => ['server' => 'Failed to register the user. Please try again.']], 500);
        }
    }
}
