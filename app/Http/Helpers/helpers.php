<?php

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


function getTenantName()
{

    $tenant = Tenant::where('id', tenant('id'))->first();

    if ($tenant) {
        // dd($tenant);
        return $tenant ? $tenant->id : config('app.name', 'Laravel');
    } else {
        return config('app.name', 'Laravel');
    }
}

function getTenantUrl()
{
    $tenant = Tenant::where('id', tenant('id'))->first();
    if ($tenant) {
        $domain = DB::connection('mysql')->table('domains')->where('tenant_id', tenant('id'))->first();

        if ($domain && isset($domain->domain)) {
            // Ensure the domain includes the correct protocol
            $protocol = request()->secure() ? 'https://' : 'http://';
            return $protocol . $domain->domain;
        }
    }
    return config('app.urls');
}

// function belongtenants()
// {
//     $tenantId = tenant('id');
//     $tenant = Tenant::find($tenantId);

//     if (!$tenant) {
//         return null;
//     }

//     $user = Auth::user();
//     if ($user && $user->tenant_id === $tenantId) {
//         return $user;
//     }
//     return null;
// }

