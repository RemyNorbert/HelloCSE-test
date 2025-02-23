<?php

namespace App\Jobs;

use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UploadProfileImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $profileId;
    private UploadedFile $image;

    public function __construct(int $profileId, UploadedFile $image)
    {
        $this->profileId = $profileId;
        $this->image = $image;
    }

    public function handle(): void
    {
        $profile = Profile::find($this->profileId);

        if (!$profile) {
            return; // Éviter de stocker une image sans profil
        }

        // Supprimer l'ancienne image si elle existe
        Storage::disk('public')->delete($profile->image);

        // Stocker l’image et récupérer le path
        $imagePath = $this->image->store('profiles', 'public');

        // Mettre à jour l’image dans la base de données
        $profile->update(['image' => $imagePath]);
    }
}
