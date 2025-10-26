<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Transactiondetail;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::all();
        return response()->json([
            'Message' => 'Success',
            'Data' => $transactions
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'qty' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $user = JWTAuth::user();
            $totalqty = array_sum($request->qty);
            $ref_code = 'TRX-' . rand(1000, 100000) . time();

            $transaction = Transaction::create([
                'ref' => $ref_code,
                'user_id' => $user->id,
                'qty' => $totalqty,
                'total' => 0,
                'status' => 'PENDING',
            ]);
            $details = [];
            $totalharga = 0;
            for ($i = 0; $i < count($request->product_id); $i++) {
                $product_id = $request->product_id[$i];
                $qty = $request->qty[$i];
                $product = Product::select('price')->find($product_id);
                $price = $product->price;
                $subtotal = $price * $qty;
                $totalharga += $subtotal;

                $details[] = Transactiondetail::create([
                    'ref_id' => $ref_code,
                    'user_id' => $user->id,
                    'product_id' => $product_id,
                    'qty' => $qty,
                    'total' => $subtotal,
                ]);
            }

            $transaction->update(['total' => $totalharga]);

            DB::commit();

            return response()->json([
                'Message' => 'Success',
                'Transaction' => $transaction,
                'Details' => $details
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'Message' => 'Gagal membuat transaksi.',
                'Error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer|exists:products,id',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1|max:5',
        ]);

        if (count($request->product_id) !== count($request->qty)) {
            return response()->json([
                'Message' => 'Jumlah product_id dan qty tidak sama.',
            ], 422);
        }

        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['Message' => 'Transaksi tidak ditemukan.'], 404);
        }

        if ($transaction->status !== 'PENDING') {
            return response()->json(['Message' => 'Transaksi tidak dapat diubah karena statusnya bukan PENDING.'], 403);
        }

        DB::beginTransaction();

        try {
            $user = JWTAuth::user();
            $calculated_total = 0;
            $total_qty = 0;
            $ref_code = 'TRX-' . rand(1000, 100000) . time();
            Transactiondetail::where('ref_id', $transaction->ref)->delete();

            $details = [];
            for ($i = 0; $i < count($request->product_id); $i++) {
                $product_id = $request->product_id[$i];
                $qty = $request->qty[$i];

                $product = Product::select('price')->find($product_id);
                $price = $product->price;
                $subtotal = $price * $qty;

                $calculated_total += $subtotal;
                $total_qty += $qty;

                $details[] = Transactiondetail::create([
                    'ref_id' => $transaction->ref,
                    'user_id' => $user->id,
                    'product_id' => $product_id,
                    'qty' => $qty,
                    'total' => $subtotal,
                ]);
            }

            $transaction->update([
                'qty' => $total_qty,
                'total' => $calculated_total,
            ]);

            DB::commit();

            return response()->json([
                'Message' => 'Transaksi berhasil diperbarui.',
                'Transaction' => $transaction->fresh(),
                'Details' => $details
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'Message' => 'Gagal memperbarui transaksi.',
                'Error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ref)
    {
        $transaction = Transaction::where('ref', $ref)->first();

        if (!$transaction) {
            return response()->json(['Message' => 'Transaksi tidak ditemukan.'], 404);
        }

        DB::beginTransaction();

        try {
            $deleted_details_count = Transactiondetail::where('ref_id', $transaction->ref)->delete();

            $transaction->delete();

            DB::commit();

            return response()->json([
                'Message' => 'Transaksi dan ' . $deleted_details_count . ' detail berhasil dihapus.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'Message' => 'Gagal menghapus transaksi.',
                'Error' => $e->getMessage()
            ], 500);
        }
    }
}
