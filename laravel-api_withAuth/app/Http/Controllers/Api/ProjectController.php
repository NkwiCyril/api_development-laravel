<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display all projects
     */
    public function index(): JsonResponse
    {
        try {

            $student_id = auth()->user()->id;

            $projects = Project::where('student_id', $student_id)->get();

            return response()->json([
                'message' => 'Projects retrieved successfully',
                'data' => $projects,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'error' => 'Error encountered: '.$e->getMessage(),
            ]);
        }
    }


    /**
     * Store a created project in the database
     * 
     * @param object $request
     */
    public function store(Request $request): JsonResponse
    {
        // validate studetent
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'duration' => 'required',
        ]);

        try {

            if (auth()->user()) {
                $student_id = auth()->user()->id;

                $project = Project::create([
                    'name' => $request->name,
                    'student_id' => $student_id,
                    'description' => $request->description,
                    'duration' => $request->duration,
                ]);

                return response()->json([
                    'message' => 'Project created successfully',
                    'status' => 1,
                    'data' => $project,
                ]);

            } else {
                return response()->json([
                    'message' => 'Unauthenticated',
                    'status' => 0,
                ], 401);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to create project. Try again!',
                'error' => 'Error encountered: '.$e->getMessage(),
                'status' => 0,
            ], 500);
        }
    }


    /**
     * Display the details of a project
     * 
     * @param string $id
     */
    public function show(string $id): JsonResponse
    {
        try {
            $project = Project::where([
                'id' => $id,
                'student_id' => auth()->user()->id,
            ]);

            if ($project->exists()) {

                $project = $project->first();

                return response()->json([
                    'status' => 1,
                    'message' => 'Project retrieved successfully',
                    'data' => $project,
                ]);
            } else {
                return response()->json([
                    'error' => 'Project not found',
                    'status' => 0,
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error encountered: '.$e->getMessage(),
                'status' => 0,
            ]);
        }
    }


    /**
     * Delete a project from the database
     * 
     * @param string $id 
     */
    public function destroy(string $id): JsonResponse
    {
        $project = Project::where([
            'id' => $id,
            'student_id' => auth()->user()->id,
        ]);

        try {

            if ($project->exists()) {
                $project = $project->first();

                $project->delete();
                
                return response()->json([
                   'message' => 'Project deleted successfully',
                   'status' => 1,
                ]);
            } else {
                return response()->json([
                    'message' => 'Project not found',
                    'status' => 0,
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'error' => 'Internal server error: ' . $e->getMessage(),
            ]);
        }
    }
}
