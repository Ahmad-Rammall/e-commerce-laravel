<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\SellerProduct;
use App\Models\Seller;

class ProductController extends Controller
{
    private $seller_id;
    public function __construct()
    {
        $this->seller_id = $this->verifySeller();
    }

    public function verifyUser()
    {
        $current_user = Auth::user();
        if (!$current_user) return false;
        return $current_user;
    }
    public function verifySeller()
    {
        $current_user = $this->verifyUser();
        if ($current_user) {
            if ($current_user->user_type == 1) {
                $seller = Seller::where("user_id", $current_user->id)->first();
                return $seller->id;
            }
        }
        return false;
    }

    public function getAllProducts()
    {
        if (!$this->verifyUser()) {
            return response()->json([
                'status' => 'Unauthorized',
            ], 401);
        }
        $products = DB::select('
        SELECT seller_products.*, products.name
        FROM seller_products
        JOIN products ON seller_products.product_id = products.id
    ');
        return response()->json([
            'products' => $products,
        ], 200);
    }

    public function getProductById($id)
    {
        if (!$this->verifyUser()) {
            return response()->json([
                'status' => 'Unauthorized',
            ], 401);
        }

        $product = DB::select('
        SELECT seller_products.*, products.name
        FROM seller_products
        JOIN products ON seller_products.product_id = products.id
        WHERE seller_products.id = :id
    ', ['id' => $id]);

        return response()->json([
            'products' => $product,
        ], 200);
    }
    public function addProduct(Request $request)
    {
        if (!$this->seller_id) {
            return response()->json([
                'status' => 'Unauthorized',
            ], 401);
        }

        $product = Product::create([
            "name" => $request->name,
        ]);
        if ($product) {
            $seller_product = SellerProduct::create([
                'seller_id' => $this->seller_id,
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
            ], 200);
        }

        return response()->json([
            'status' => 'error',
        ], 404);
    }

    public function deleteProduct(Request $request)
    {
        $product = SellerProduct::where('id', $request->seller_product_id)
            ->where('seller_id', $this->seller_id)
            ->first();

        if ($product) {
            $product->delete();
            return response()->json([
                'status' => 'product deleted successfully!',
                'product' => $product,
            ]);
        }
        return response()->json([
            'status' => 'Not Authorized',
        ], 401);
    }

    public function updateProduct(Request $request)
    {
        $product = SellerProduct::where('id', $request->seller_product_id)
            ->where('seller_id', $this->seller_id)
            ->first();

        if ($product) {

            $product->update([
                'price' => $request->price,
                'quantity' => $request->quantity,
                'description' => $request->description,
            ]);

            return response()->json([
                'status' => 'product updated successfully!',
                'product' => $product,
            ]);
        }
        return response()->json([
            'status' => 'Not Authorized',
        ], 401);
    }
}
