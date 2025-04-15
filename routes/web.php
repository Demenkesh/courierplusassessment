<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Admin\AdminController;

Route::get('/', function () {
    return redirect('/api/documentation');
});
