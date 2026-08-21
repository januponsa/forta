<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DefenseCase;
use Illuminate\Auth\Access\HandlesAuthorization;

class DefenseCasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the mentor document.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DefenseCase  $defense
     * @return mixed
     */
    public function viewMentorDocument(User $user, DefenseCase $defense)
    {
        // For admin in this app, if they reach here via web guard, they are authorized.
        return true; 
    }
}
