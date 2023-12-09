<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SellerProduct;
use App\Models\Seller;

class ProductController extends Controller
{
    public function verifySeller($request)
    {
        $seller = Seller::find($request->seller_id);
        if (!$seller) {
            return false;
        }
        return true;
    }
    public function addProduct(Request $request)
    {
        if(!$this->verifySeller($request)) {
            return response()->json([
                'status' => 'errorss',
            ], 404);
        }
 
        $product = Product::create([
            "name" => $request->name,
        ]);
        if ($product) {
            $seller_product = SellerProduct::create([
                'seller_id' => $request->seller_id,
                'product_id' => $product->id,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'description' => $request->description
            ]);
        }

        if ($seller_product) {
            return response()->json([
                'status' => 'product added successfully!',
                'product' => $seller_product,
            ]);
        }

        return response()->json([
            'status' => 'error',
        ], 404);
    }
}
