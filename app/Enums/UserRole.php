<?php

namespace App\Enums;

enum UserRole: string
{
    case Owner = 'owner';
    case BranchAdmin = 'branch_admin';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Owner',
            self::BranchAdmin => 'Admin Cabang',
        };
    }
}
