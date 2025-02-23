<?php

namespace App\Commands;

use App\Data\ProfileData;
use App\Models\Profile;

class UpdateProfileCommand
{
    public function __construct(
        public readonly Profile $profile,
        public readonly ProfileData $data,
        public readonly string $newImagePath
    ) {}

    public function handle(): void
    {
        // Mise à jour du profil en excluant l'image (gérée séparément)
        $data = [
            'image' => $this->newImagePath,
            'first_name' => $this->data->firstName,
            'last_name' => $this->data->lastName,
            'status' => $this->data->status,
        ];

        $this->profile->update($data);
    }
}
