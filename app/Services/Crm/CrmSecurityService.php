<?php

namespace App\Services\Crm;

use Illuminate\Support\Facades\Hash;

class CrmSecurityService
{
    public function updatePassword($admin, string $newPassword): void
    {
        $admin->password = Hash::make($newPassword);
        $admin->save();
    }
}
