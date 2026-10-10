<?php

use App\Http\Controllers\Api\PublicInventoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('public')->group(function () {
    Route::get('tires',          [PublicInventoryController::class, 'tires']);
    Route::get('tires/filters',  [PublicInventoryController::class, 'filters']);

    Route::prefix('vehicles')->group(function () {
        Route::get('makes',     [PublicInventoryController::class, 'vehicleMakes']);
        Route::get('models',    [PublicInventoryController::class, 'vehicleModels']);
        Route::get('years',     [PublicInventoryController::class, 'vehicleYears']);
        Route::get('tire-size', [PublicInventoryController::class, 'vehicleTireSize']);
    });
});
