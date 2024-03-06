<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return Payment::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'amount' => 'required|numeric|min:0',
        ]);

        $payment = Payment::create([
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        return response()->json($payment, 201);
    }

    public function show($id)
    {
        return Payment::findOrFail($id);
    }
}
