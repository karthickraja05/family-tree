<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FamilyTreeController;
use App\Http\Controllers\PersonController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [FamilyTreeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('persons', PersonController::class);
    Route::get('/family_tree', [FamilyTreeController::class, 'familyTree']);
    Route::get('/persons/{id}/tree-pdf', [PersonController::class, 'treePdf'])->name('persons.tree.pdf');
    Route::get('/persons/{id}/tree-view', [PersonController::class, 'treeView'])->name('persons.tree.view');

    Route::group([
    'prefix' => 'root',
    ], function () {
        Route::post('/add_root', [FamilyTreeController::class, 'addRoot']);
        Route::post('/add', [FamilyTreeController::class, 'add']);
        Route::get('/get_tree', [FamilyTreeController::class, 'getTree']);
        Route::get('/view_user', [FamilyTreeController::class, 'viewUser']);
    });
});

require __DIR__.'/auth.php';