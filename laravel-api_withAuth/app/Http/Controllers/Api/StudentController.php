<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    public function register(Request $request)
    {
        $validatedRequest = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:students',
            'password' => 'required:confirmed',
            'phone_number' => 'sometimes|string|min:10|max:25',
        ]);

        try {
            Student::create($validatedRequest);

            return response()->json([
                'message' => 'Registration Successful!',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error. Please check your input.',
                'status' => 0,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 0,
            ], 500);
        }

    }

    public function login(Request $request)
    {

    }

    public function logout()
    {

    }

    public function profile()
    {

    }
}
