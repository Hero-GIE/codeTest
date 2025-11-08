<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdventureController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'title'    => 'required|string|max:255',
                'excerpt'  => 'required|string',
                'image'    => 'required|url',
                'date'     => 'required|date',
                'location' => 'nullable|string|max:255',
            ]);

            // TODO: Save to your database
            // For now, we'll just return success
            $adventure = [
                'id'        => uniqid(), // Generate a temporary ID
                 ...$validated,
                'createdAt' => now()->toISOString(),
            ];

            return response()->json([
                'success'   => true,
                'id'        => $adventure['id'],
                'adventure' => $adventure,
                'message'   => 'Adventure created successfully',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error'   => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => 'Failed to create adventure: ' . $e->getMessage(),
            ], 500);
        }
    }
}
