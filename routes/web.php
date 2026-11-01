<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/peta', function () {
    return view('peta');
})->name('peta');

Route::get('/proyek', function () {
    return view('proyek.index');
})->name('proyek.index');

Route::get('/proyek/{id}', function ($id) {
    return view('proyek.detail', ['id' => $id]);
})->name('proyek.detail');

Route::get('/statistik', function () {
    return view('statistik');
})->name('statistik');

// API endpoint untuk data proyek (dummy)
Route::get('/api/proyek', function () {
    return response()->json(config('proyek'));
})->name('api.proyek');
