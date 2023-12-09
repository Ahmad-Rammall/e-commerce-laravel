<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\UserType;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/' , function (){
    $type = new UserType();
    $type->type_name = 'seller';
    $type->save();
    $type2 = new UserType();
    $type2->type_name = 'user';
    $type2->save();
});
Route::post('/login' , [AuthController::class , 'login']);
Route::post('/register' , [AuthController::class , 'register']);
Route::get('/logout' , [AuthController::class , 'logout']);

Route::post('/addproduct' , [ProductController::class , 'addProduct']);



