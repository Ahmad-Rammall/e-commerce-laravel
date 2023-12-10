<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\OrderDetail;


class TransactionController extends Controller
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
    public function getTransaction($id)
    {
        $transaction = Transaction::where("id", $id)->first();
        return response()->json([
            'transaction' => $transaction
        ]);
    }

    public function addTransaction(Request $request)
    {
        if ($this->user_id) {

            // get total order price
            $total_amount = DB::select('
            SELECT SUM(price * quantity) as total_price
            FROM order_details
            WHERE order_id = :order_id
            GROUP BY order_id;
            ', ['order_id'=> $request->order_id]);

            // create transaction
            $transaction = Transaction::create([
                'order_id' => $request->order_id,
                'amount' => $total_amount[0]->total_price,
                'date' => now()
            ]);

            if($transaction){
                return response()->json([
                    'status' => 'Transaction Added',
                    'transaction' => $transaction
                ], 200);
            }
        }

        return response()->json([
            'status' => 'Unauthorized',
        ], 401);
    }
}
