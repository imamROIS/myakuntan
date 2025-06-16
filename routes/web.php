<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



//welcome page

//route utama
Route::get('/', function () {
    return redirect()->route('filament.adminportal.pages.dashboard'); // Redirect ke dashboard Filament
});




// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('adminportal');
// });
