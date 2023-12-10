<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
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

// Users Routes
Route::post('/login' , [AuthController::class , 'login']);
Route::post('/register' , [AuthController::class , 'register']);
Route::get('/logout' , [AuthController::class , 'logout']);

// Products Routes
Route::get('/products' , [ProductController::class , 'getAllProducts']);
Route::get('/getproduct/{id}' , [ProductController::class , 'getProductById']);
Route::post('/addproduct' , [ProductController::class , 'addProduct']);
Route::post('/updateproduct' , [ProductController::class , 'updateProduct']);
Route::post('/deleteproduct' , [ProductController::class , 'deleteProduct']);

// Carts Routes
Route::get('/cart/{id}' , [CartController::class , 'getCartProducts']);
Route::post('/addToCart' , [CartController::class , 'addToCart']);
Route::post('/removeFromCart' , [CartController::class , 'removeFromCart']);

// Orders Routes
Route::get('/order/{id}' , [OrderController::class , 'getOrder']);
Route::post('/makeOrder' , [OrderController::class , 'makeOrder']);