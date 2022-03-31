<?php

Route::prefix('backstage')->middleware('setActiveCampaign')->group(function () {
    // Account activation
    Route::get('activate/{ott}', [Auth\ActivateAccountController::class, 'index'])->name('backstage.activate.show');
    Route::put('activate/{ott}', [Auth\ActivateAccountController::class, 'update'])->name('backstage.activate.update');

    // Authentication
    Auth::routes([
        'register' => false,
    ]);

    Route::namespace('Backstage')->name('backstage.')->middleware('auth')->group(function () {

use App\Http\Controllers\Auth;
use App\Http\Controllers\Backstage;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;
        // Campaigns
        Route::get('campaigns/{campaign}/use', [Backstage\CampaignsController::class, 'use'])->name('campaigns.use');
        Route::resource('campaigns', 'CampaignsController');

        // Dashboard
        Route::resource('/', 'DashboardController');
        Route::resource('dashboard', 'DashboardController');

        // Users
        Route::resource('users', 'UsersController');

        Route::middleware('redirectIfNoActiveCampaign')->group(function () {
            Route::resource('prizes', 'PrizesController');
            Route::resource('games', 'GamesController');
        });
    });
});

Route::get('{campaign:slug}', [FrontendController::class, 'loadCampaign']);
Route::get('/', [FrontendController::class, 'placeholder']);
