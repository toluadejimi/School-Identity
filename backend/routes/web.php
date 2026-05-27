<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $basePath = trim(request()->getBaseUrl(), '/');

    return redirect($basePath === '' ? '/admin' : "/{$basePath}/admin");
});
