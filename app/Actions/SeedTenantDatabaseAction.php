<?php

namespace App\Actions;

use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;

class SeedTenantDatabaseAction
{
    public function execute(Tenant $tenant)
    {
        Artisan::call("tenants:artisan \"db:seed --database=tenant --class=TenantUsersTableSeeder\" --tenant={$tenant->id}");
    }
}
