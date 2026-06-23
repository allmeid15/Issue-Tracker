<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommentController;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('projects', ProjectController::class);
Route::resource('projects.issues', IssueController::class);
Route::resource('tags', TagController::class)->only(['index', 'store', 'destroy']);

//Ajax routes
Route::get('/issues/{issue}/comments', [CommentController::class, 'index'])->name('issues.comments.index');
Route::post('/issues/{issue}/comments', [CommentController::class, 'store'])->name('issues.comments.store');
