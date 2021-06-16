<?php

namespace App\Actions;

use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;

class MigrateTenantDatabaseAction
{
    public function execute(Tenant $tenant)
    {
        Artisan::call("tenants:artisan \"migrate --database=tenant --path=database/migrations/tenant\" --tenant={$tenant->id}");
    }
}
