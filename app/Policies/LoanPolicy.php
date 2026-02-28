<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Loan $loan): bool
    {
        return $user->id === $loan->user_id || $user->isAdmin() || $user->isStaff();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isBorrower();
    }

    /**
     * Determine whether the user can approve loans (staff only).
     */
    public function isStaff(User $user): bool
    {
        return $user->isStaff() || $user->isAdmin();
    }
}
