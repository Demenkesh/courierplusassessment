<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * Specify the connection for the central database.
     *
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * Specify the table name.
     *
     * @var string
     */
    protected $table = 'personal_access_tokens';
}
