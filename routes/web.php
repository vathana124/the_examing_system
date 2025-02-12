<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // redirect to login page
    return redirect('/admin');
});
