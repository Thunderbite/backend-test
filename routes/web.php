<?php

Route::prefix('backstage')->middleware('setActiveCampaign')->group(function () {
    // Account activation
    Route::get('activate/{ott}', 'Auth\ActivateAccountController@index')->name('backstage.activate.show');
    Route::put('activate/{ott}', 'Auth\ActivateAccountController@update')->name('backstage.activate.update');

    // Authentication
    Auth::routes([
        'register' => false,
    ]);

    Route::namespace('Backstage')->name('backstage.')->middleware('auth')->group(function () {
        // Campaigns
        Route::get('campaigns/{campaign}/use', 'CampaignsController@use')->name('campaigns.use');
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

Route::get('{campaign:slug}', 'FrontendController@loadCampaign');
Route::get('/', 'FrontendController@placeholder');
