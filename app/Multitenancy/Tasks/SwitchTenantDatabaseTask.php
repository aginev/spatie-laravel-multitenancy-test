<?php

namespace App\Multitenancy\Tasks;

use Illuminate\Support\Facades\DB;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Exceptions\InvalidConfiguration;
use Spatie\Multitenancy\Tasks\SwitchTenantDatabaseTask as SwitchTenantDatabaseTaskBase;

class SwitchTenantDatabaseTask extends SwitchTenantDatabaseTaskBase
{

    public function makeCurrent(Tenant $tenant): void
    {
        $this->setTenantConnectionDatabase($tenant);
    }

    protected function setTenantConnectionDatabase(?Tenant $tenant)
    {
        $tenantConnectionName = $this->tenantDatabaseConnectionName();
        $databaseName = $tenant ? $tenant->getDatabaseName() : null;

        if ($tenantConnectionName === $this->landlordDatabaseConnectionName()) {
            throw InvalidConfiguration::tenantConnectionIsEmptyOrEqualsToLandlordConnection();
        }

        if (is_null(config("database.connections.{$tenantConnectionName}"))) {
            throw InvalidConfiguration::tenantConnectionDoesNotExist($tenantConnectionName);
        }

        config([
            "database.connections.{$tenantConnectionName}.database" => $databaseName,
            //"database.connections.{$tenantConnectionName}.host" => $tenant->database_host ?? null,
            //"database.connections.{$tenantConnectionName}.post" => $tenant->database_port ?? null,
            //"database.connections.{$tenantConnectionName}.username" => $tenant->database_username ?? null,
            //"database.connections.{$tenantConnectionName}.password" => $tenant->database_password ?? null,
        ]);

        app('db')->extend($tenantConnectionName, function ($config, $name) use ($databaseName) {
            $config['database'] = $databaseName;

            return app('db.factory')->make($config, $name);
        });

        DB::purge($tenantConnectionName);
    }

}
