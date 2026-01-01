<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\FamilyTreeController;

Route::get('/hello', function () {
    return response()->json([
        'message' => 'API is working 🎉'
    ]);
});



// Route::get('/persons', [PersonController::class, 'list']);
// Route::post('/persons', [PersonController::class, 'store']);
// Route::get('/persons/{id}', [PersonController::class, 'show']);
// Route::post('/persons/{id}/spouse', [PersonController::class, 'addSpouse']);
// Route::post('/persons/{id}/child', [PersonController::class, 'addChild']);
// Route::get('/persons/{id}/available-children', [PersonController::class, 'availableChildren']);

Route::group([
    'prefix' => 'root',
], function () {
    Route::post('/add_root', [FamilyTreeController::class, 'addRoot']);
    Route::post('/add', [FamilyTreeController::class, 'add']);
    Route::get('/get_tree', [FamilyTreeController::class, 'getTree']);
    Route::get('/view_user', [FamilyTreeController::class, 'viewUser']);
});

