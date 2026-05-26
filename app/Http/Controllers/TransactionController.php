<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $wallet = $request->user()->wallet;

        $transactions = $wallet->transactions()
                            ->latest()
                            ->paginate(10);

        return response()->json($transactions);
    }
}