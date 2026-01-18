<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/user', function () {
    return User::where('id', 1)->first();
});
