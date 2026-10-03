<?php

declare(strict_types=1);

use App\Modules\Locations\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

Route::get('locations/search', LocationController::class)->name('locations.search');
