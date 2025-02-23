<?php

namespace App\Enums;

enum EProfileStatus :string {
    case Inactive = 'inactive';
    case Pending = 'pending';
    case Active = 'active';

    public function toString(): string {
        return match ($this) {
            self::Inactive => 'inactive',
            self::Pending => 'pending',
            self::Active => 'active',
        };
    }
}
