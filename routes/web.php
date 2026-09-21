<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/api-docs', '/api/documentation')->name('api.docs');
