<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\User;

class OrderController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $find = Medicine::find($id);
        if ($find) {
            if ($request['quantity'] <= $find['quantity']) {
                $order = [
                    'name' => $request['name'],
                    'medicine' => $find['name'],
                    'quantity' => $request['quantity'],
                    'price' => $request['quantity'] * $find['price'],
                    'phone_number' => $request['phone_number'],
                    'prepare_status' => 0,
                    'payment_status' => 0
                ];

                $find['quantity'] = $find['quantity'] - $request['quantity'];
                $find->save();

                Order::create($order);
                return response()->json([
                    "message" => "success"
                ]);
            } else {
                return response()->json([
                    "message" => "error"
                ]);
            }
        } else {
            return response()->json([
                "message" => "error"
            ]);
        }
    }
    public function show_order(Request $request)
    {
        $user = User::where('token', $request['token']);

        return response()->json([
            'heading' => 'Latest orders',
            'orders' => Order::latest()->where('name', $user['name'])->get()
        ]);
    }

    public function show_order_admin()
    {
        return response()->json([
            'heading' => 'Latest orders',
            'orders' => Order::latest()->get()
        ]);
    }

    public function change_payment($id)
    {
        $order = Order::find($id);
        if ($order['payment_status'] == 0) {
            $order['payment_status'] = 1;
            $order->save();
            return response()->json([
                'message' => 'success'
            ]);
        }
        return response()->json([
            'message' => 'error'
        ]);
    }

    public function change_prepare($id)
    {
        $order = Order::find($id);
        if ($order['prepare_status'] == 0) {
            $order['prepare_status'] = 1;
            $order->save();
            return response()->json([
                'message' => 'success'
            ]);
        } else if ($order['prepare_status'] == 1) {
            $order['prepare_status'] = 2;
            $order->save();
            return response()->json([
                'message' => 'success'
            ]);
        } else {
            return response()->json([
                'message' => 'error'
            ]);
        }
    }
}