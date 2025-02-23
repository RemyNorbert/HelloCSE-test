<?php

namespace App\Console\Commands;

use App\Models\Profile;
use Illuminate\Console\Command;

class CreateProfileCommand extends Command
{
    protected $signature = 'profile:create {lastName} {firstName} {status} {image_path}';
    protected $description = 'Créer un profil avec les informations fournies.';

    public function handle()
    {
        $lastName = $this->argument('lastName');
        $firstName = $this->argument('firstName');
        $statut = $this->argument('status');
        $imagePath = $this->argument('image_path');

        $profile = Profile::create([
            'last_name' => $lastName,
            'first_name' => $firstName,
            'image' => $imagePath,
            'status' => $statut,
        ]);

        $this->info("Profil {$profile->id} créé avec succès !");
    }
}
