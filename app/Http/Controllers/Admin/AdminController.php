<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tenant;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        dd('hello');
        // $numbers = Numbers::select('company_name')
        //     ->groupBy('company_name')
        //     ->paginate('50');

        return view('admin.dashboard.index', compact('numbers'));
    }
}
