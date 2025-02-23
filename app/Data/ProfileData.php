<?php

namespace App\Data;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\Image;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class ProfileData extends Data
{
    public function __construct(
        #[Required, StringType]
        public string $lastName,

        #[Required, StringType]
        public string $firstName,

        #[Required, Image, Max(2048)] // Max 2MB
        public UploadedFile $image,

        #[Required, In(['active', 'pending', 'inactive'])]
        public string $status
    ) {}
}
