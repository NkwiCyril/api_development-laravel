<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $employees = Employee::get();

            return response()->json([
                'message' => 'Listing employees...',
                'status' => 1,
                'data' => $employees,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to retrieve employees. Try again!',
                'error' => 'Error encountered: '.$e->getMessage(),
                'status' => 0,
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedRequest = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:50',
            'phone_number' => 'required|string|min:10|max:25',
            'gender' => 'required|string|in:male,female,others',
            'age' => 'required|integer',
        ]);

        // Using the create method to create a new employee
        // Requires the fillable array in the Employee model

        try {
            Employee::create($validatedRequest);

            return response()->json([
                'message' => 'Employee created successfully!',
                'status' => 1,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error. Please check your input.',
                'status' => 0,
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to create employee. Try again!',
                'error' => 'Error encountered: '.$e->getMessage(),
                'status' => 0,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $employee = Employee::findOrFail($id);

            return response()->json([
                'message' => 'Employee found',
                'status' => 1,
                'data' => $employee,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Employee not found. Try again!',
                'error' => 'Error encountered: '.$e->getMessage(),
                'status' => 0,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to retrieve employee. Try again!',
                'error' => 'Error encountered: '.$e->getMessage(),
                'status' => 0,
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $employee = Employee::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|max:255',
                'phone_number' => 'sometimes|string|max:20',
                'gender' => 'sometimes|in:male,female,others',
                'age' => 'sometimes|integer|min:18|max:100',
            ]);

            $employee->name = $validatedData['name'] ?? $employee->name;
            $employee->email = $validatedData['email'] ?? $employee->email;
            $employee->phone_number = $validatedData['phone_number'] ?? $employee->phone_number;
            $employee->gender = $validatedData['gender'] ?? $employee->gender;
            $employee->age = $validatedData['age'] ?? $employee->age;

            $employee->save();

            return response()->json([
                'message' => 'Employee updated successfully!',
                'status' => 1,
                'data' => $employee,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Employee not found. Try again!',
                'status' => 0,
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error. Please check your input.',
                'error' => $e->errors(),
                'status' => 0,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to update employee. Try again!',
                'error' => 'Error encountered: '.$e->getMessage(),
                'status' => 0,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $employee = Employee::findOrFail($id);

            $employee->delete();

            return response()->json([
                'message' => 'Employee deleted successfully!',
                'status' => 1,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Employee not found. Try again!',
                'error' => $e->getMessage(),
                'status' => 0,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to delete employee. Try again!',
                'status' => 0,
            ], 500);
        }
    }
}
