<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->role === 'karyawan';
    }

    public function view(User $user, LeaveRequest $request): bool
    {
        return $user->isAdmin() || $user->employee?->id === $request->employee_id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'karyawan';
    }

    public function update(User $user, LeaveRequest $request): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, LeaveRequest $request): bool
    {
        return $user->isAdmin();
    }
}
