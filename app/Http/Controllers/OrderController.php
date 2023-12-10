<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Order;
use App\Models\OrderDetail;

class OrderController extends Controller
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
    public function getOrder($id)
    {
        if (!$this->user_id) {
            return response()->json([
                'status' => 'Unauthorized',
            ], 401);
        }
        // $order = Order::find($id);
        $order = DB::select('
        SELECT orders.user_id, order_details.*
        FROM orders,order_details
        WHERE order_details.order_id = orders.id AND orders.id = :id
    ', ['id' => $id]);

        if ($order) {
            return response()->json([
                'order' => $order,
            ]);
        }

        return response()->json([
            'status' => 'Order Not Found',
        ], 404);
    }

    public function makeOrder(Request $request)
    {
        if ($this->user_id) {
            $order = Order::create([
                'user_id' => $this->user_id,
            ]);

            if ($order) {
                $order_details = OrderDetail::create([
                    'order_id' => $order->id,
                    'seller_product_id' => $request->seller_product_id,
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                    'shipping_address' => $request->shipping_address
                ]);

                if ($order_details) {
                    return response()->json([
                        'status' => 'Order Added',
                        'order' => $this->getOrder($order->id),
                    ]);
                }
                $order->delete();
            }
        }
        return response()->json([
            'status' => 'Unauthorized',
        ], 401);
    }
}
