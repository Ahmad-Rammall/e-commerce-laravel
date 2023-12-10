<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CartProduct;
use App\Models\Cart;
use App\Models\SellerProduct;

class CartController extends Controller
{
    private $user_id;
    public function __construct()
    {
        $this->user_id = $this->verifyUser();
    }
    public function verifyUser()
    {
        $current_user = Auth::user();
        if (!$current_user) return false;
        return $current_user->id;
    }

    public function getCartProducts($id)
    {
        if ($this->user_id) {
            $products = CartProduct::where('cart_id', $id)->first();

            return response()->json(['cart' => $products]);
        }
        return response()->json(['status' => 'Not Authorized'], 401);
    }

    public function addToCart(Request $request)
    {
        if ($this->user_id) {
            if ($this->user_id == $request->user_id) {
                // Get Cart
                $cart = Cart::where('user_id', $this->user_id)->first();
                //Get Product To add
                $product = SellerProduct::where('id', $request->seller_product_id)->first();

                $productCart = CartProduct::create([
                    'cart_id' => $cart->id,
                    'seller_product_id' => $request->seller_product_id,
                    'quantity' => $request->quantity,
                    'price' => intval($product->price) * intval($request->quantity)
                ]);

                if ($productCart) {
                    return response()->json([
                        'status' => 'product added successfully to cart!',
                        'product' => $productCart,
                    ], 200);
                }
            }
        }
        return response()->json(['status' => 'Not Authorized'], 401);
    }

    public function removeFromCart(Request $request)
    {
        if ($this->user_id) {
            if ($this->user_id == $request->user_id) {
                // Get Cart
                $cart = Cart::where('user_id', $this->user_id)->first();
                $productCart = CartProduct::where('id', $request->cart_product_id)
                    ->where('cart_id', $cart->id)
                    ->first();
                if ($productCart) {
                    $productCart->delete();
                    return response()->json([
                        'status' => 'product deleted successfully to cart!',
                        'product' => $productCart,
                    ], 200);
                }
            }
        }
        return response()->json(['status' => 'Not Authorized'], 401);
    }
}
