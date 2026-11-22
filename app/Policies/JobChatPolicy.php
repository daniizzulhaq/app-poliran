<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\JobChat;
use App\Models\User;

class JobChatPolicy
{
    public function view(User $user, JobChat $chat): bool
    {
        return $user->isSuperAdmin()
            || ($user->isRole(Role::PencariKerja) && $chat->user_id === $user->id);
    }
}
