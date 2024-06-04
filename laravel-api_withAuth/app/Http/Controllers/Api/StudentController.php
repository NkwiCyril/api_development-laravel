<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    /**
     * Register a new student
     *
     * @param  object  $request
     */
    public function register(Request $request): JsonResponse
    {
        $validatedRequest = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:students',
            'password' => 'required:confirmed',
            'phone_number' => 'sometimes|string|min:10|max:25',
        ]);

        try {
            $student = new Student();

            $student->name = $validatedRequest['name'];
            $student->email = $validatedRequest['email'];
            $student->password = bcrypt($validatedRequest['password']);
            $student->phone_number = $validatedRequest['phone_number'] ?? null;

            $student->save();

            return response()->json([
                'message' => 'Registration Successful!',
                'status' => 1,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 0,
            ], 500);
        }

    }

    /**
     * Login user with email and password
     *
     * @param  object  $request
     */
    public function login(Request $request): JsonResponse
    {
        $validatedRequest = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {

            $student = Student::where('email', $validatedRequest['email'])->first();

            if (isset($student)) {

                if (Hash::check($validatedRequest['password'], $student->password)) {
                    $token = $student->createToken('auth_token')->plainTextToken;

                    return response()->json([
                        'message' => 'Student logged in successful!',
                        'status' => 1,
                        'access_token' => $token,
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Incorrect password',
                        'status' => 0,
                    ], 401);
                }

            } else {
                return response()->json([
                    'message' => 'Student does not exist',
                    'status' => 0,
                ], 401);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error encountered: '.$e->getMessage(),
                'status' => 0,
            ], 500);
        }

    }

    public function logout(): JsonResponse
    {
        try {
            auth()->user()->tokens()->delete();

            return response()->json([
                'status' => 1,
                'message' => 'Student logged out successful!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error encountered: '.$e->getMessage(),
            ], 500);
        }
    }

    public function profile(): JsonResponse
    {

        try {
            if (auth()->user()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Student profile information',
                    'data' => auth()->user(),
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Unauthenticated student',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'error' => 'Error encountered: '.$e->getMessage(),
            ]);
        }

    }
}
