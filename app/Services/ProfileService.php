<?php

namespace App\Services;

use App\Commands\UpdateProfileCommand;
use App\Data\ProfileData;
use App\Models\Profile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function createProfile(ProfileData $data): void
    {
        // Stocker l’image et récupérer le chemin
        $imagePath = $data->image->store('profiles', 'public');

        // Exécuter la commande de création
        Artisan::call('profile:create', [
            'lastName' => $data->lastName,
            'firstName' => $data->firstName,
            'status' => $data->status,
            'image_path' => $imagePath,
        ]);
    }

    public function updateProfile(Profile $profile, ProfileData $data): void
    {
        // Supprimer l'ancienne image
        Storage::disk('public')->delete($profile->image);

        // Sauvegarder la nouvelle image et récupérer le chemin
        $path = $data->image->store('profiles', 'public');

        // Dispatch la mise à jour via une commande
        Bus::dispatch(new UpdateProfileCommand($profile, $data, $path));
    }

    public function deleteProfile(Profile $profile): void
    {
        // Supprimer l'ancienne image avant de supprimer le profil
        Storage::disk('public')->delete($profile->image);

        // Supprimer le profil
        $profile->delete();
    }
}
