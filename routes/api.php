<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ExpenseController;


Route::get('/user', [UserController::class, 'allUsers']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::get('/expenses/{id}', [ExpenseController::class, 'show']);
    Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tags', [TagController::class, 'store']);
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tags/{id}', [TagController::class, 'show']);
    Route::put('/tags/{id}', [TagController::class, 'update']);
    Route::delete('/tags/{id}', [TagController::class, 'destroy']);
    Route::post('/expenses/{expenseId}/tags', [ExpenseController::class, 'addTags']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/groups', [GroupController::class, 'store']);
    Route::get('/groups', [GroupController::class, 'index']);
    Route::get('/groups/{id}', [GroupController::class, 'show']);
    Route::delete('/groups/{id}', [GroupController::class, 'destroy']);
});