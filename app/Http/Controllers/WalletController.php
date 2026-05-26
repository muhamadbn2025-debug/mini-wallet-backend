<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $wallet = $request->user()->wallet;
        return response()->json(['balance' => $wallet->balance]);
    }

    public function topup(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1|max:10000000',
        ], [
            'amount.required' => 'Nominal tidak boleh kosong.',
            'amount.integer'  => 'Nominal harus berupa angka.',
            'amount.min'      => 'Nominal harus lebih dari 0.',
            'amount.max'      => 'Nominal melebihi batas maksimum transaksi.',
        ]);

        $wallet = $request->user()->wallet;
        $wallet->increment('balance', $request->amount);

        Transaction::create([
            'wallet_id'   => $wallet->id,
            'type'        => 'topup',
            'amount'      => $request->amount,
            'description' => 'Top-up saldo',
        ]);

        return response()->json([
            'message' => 'Top-up berhasil',
            'balance' => $wallet->fresh()->balance,
        ]);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'recipient_email' => 'required|email|exists:users,email',
            'amount'          => 'required|integer|min:1|max:10000000',
        ], [
            'amount.required'        => 'Nominal tidak boleh kosong.',
            'amount.integer'         => 'Nominal harus berupa angka.',
            'amount.min'             => 'Nominal harus lebih dari 0.',
            'amount.max'             => 'Nominal melebihi batas maksimum transaksi.',
            'recipient_email.exists' => 'Penerima tidak ditemukan.',
        ]);

        $sender = $request->user();

        if ($sender->email === $request->recipient_email) {
            return response()->json([
                'message' => 'Tidak bisa transfer ke diri sendiri.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $senderWallet = \App\Models\Wallet::where('user_id', $sender->id)
                            ->lockForUpdate()->first();

            $recipient = User::where('email', $request->recipient_email)->first();

            $recipientWallet = \App\Models\Wallet::where('user_id', $recipient->id)
                            ->lockForUpdate()->first();

            if ($senderWallet->balance < $request->amount) {
                DB::rollBack();
                return response()->json(['message' => 'Saldo tidak cukup.'], 422);
            }

            $senderWallet->decrement('balance', $request->amount);
            $recipientWallet->increment('balance', $request->amount);

            Transaction::create([
                'wallet_id'       => $senderWallet->id,
                'type'            => 'transfer_out',
                'amount'          => $request->amount,
                'related_user_id' => $recipient->id,
                'description'     => 'Transfer ke ' . $recipient->name,
            ]);

            Transaction::create([
                'wallet_id'       => $recipientWallet->id,
                'type'            => 'transfer_in',
                'amount'          => $request->amount,
                'related_user_id' => $sender->id,
                'description'     => 'Transfer dari ' . $sender->name,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Transfer berhasil',
                'balance' => $senderWallet->fresh()->balance,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Transfer gagal, silakan coba lagi.'
            ], 500);
        }
    }
}