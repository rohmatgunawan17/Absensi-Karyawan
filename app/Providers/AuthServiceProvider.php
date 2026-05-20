<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Policies\AttendancePolicy;
use App\Policies\EmployeePolicy;
use App\Policies\LeaveRequestPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Employee::class => EmployeePolicy::class,
        Attendance::class => AttendancePolicy::class,
        LeaveRequest::class => LeaveRequestPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('access-admin', fn($user) => $user->role === 'admin');
    }
}
