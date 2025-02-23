<?php

namespace App\Http\Controllers;

use App\Data\ProfileData;
use App\Models\Profile;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profileService) {}

    public function index(): JsonResponse
    {
        if (request()->bearerToken() && $user = Auth::guard('sanctum')->user()) {
            Auth::setUser($user);
        }

        $isAdmin = Auth::user();
        $profiles = Profile::where('status', 'active')
            ->get($isAdmin ? ['id', 'first_name', 'last_name', 'image', 'status'] : ['id', 'first_name', 'last_name', 'image']);

        return response()->json([
            'success' => true,
            'data' => $profiles
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $profileData = ProfileData::from($request);
        $this->profileService->createProfile($profileData);

        return response()->json([
            'message' => 'Profile created successfully',
            201
        ]);
    }

    public function modifyOrDelete(Request $request, string $id): JsonResponse
    {
        $profile = Profile::findOrFail($id);

        if ($request->isMethod('delete')) {
            $this->profileService->deleteProfile($profile);
            return response()->json(['message' => 'Profile deleted'], 204);
        }

        // Transformation de la requête en DTO `ProfileData`
        $profileData = ProfileData::from($request);

        // Mise à jour du profil via le service
        $this->profileService->updateProfile($profile, $profileData);

        return response()->json(['message' => 'Profile updated successfully']);
    }
}
