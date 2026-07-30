<?php

use Illuminate\Support\Facades\Auth;
use App\Livewire\Authentication;
use App\Livewire\CreateUser;
use App\Livewire\Reservations;
use App\Livewire\TablesIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', TablesIndex::class)->name('index');
Route::get('/register', CreateUser::class)->name('register');
Route::get('/login', Authentication::class)->name('login');

Route::middleware([
    'auth',
])->group(function () {
    Route::get('/logout', function(){
        Auth::logout();
        return redirect()->route('index');
    });
    Route::get('/reservations', Reservations::class)->name('reservations');

});
