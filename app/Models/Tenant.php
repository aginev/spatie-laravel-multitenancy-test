<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

class Tenant extends \Spatie\Multitenancy\Models\Tenant
{
    use UsesLandlordConnection;

    protected $hidden = [
        'database',
        //'database_host',
        //'database_port',
        //'database_username',
        //'database_password',
        //'s3_bucket',
    ];
}
