<?php

namespace App\Http\Controllers;

use App\Events\TransactionCompleted;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $transactions = Transaction::where(function($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id);
        })
            ->with(['sender:id,name,email', 'receiver:id,name,email'])
            ->latest('created_at')
            ->paginate(20);

        $transactionData = $transactions->map(function ($transaction) use ($user) {
            return [
                'id' => $transaction->id,
                'type' => $transaction->type,
                'direction' => $transaction->receiver_id === $user->id ? 'incoming' : 'outgoing',
                'amount' => (float) $transaction->amount,
                'commission_fee' => (float) $transaction->commission_fee,
                'total_amount' => (float) $transaction->total_amount,
                'status' => $transaction->status,
                'description' => $transaction->description,
                'sender' => [
                    'id' => $transaction->sender->id,
                    'name' => $transaction->sender->name,
                    'email' => $transaction->sender->email,
                ],
                'receiver' => [
                    'id' => $transaction->receiver->id,
                    'name' => $transaction->receiver->name,
                    'email' => $transaction->receiver->email,
                ],
                'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'current_balance' => (float) $user->balance,
                'transactions' => $transactionData,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ], [
            'receiver_id.required' => 'Please enter a recipient user ID.',
            'receiver_id.integer' => 'User ID must be a number.',
            'receiver_id.exists' => 'This user does not exist.',
            'amount.required' => 'Please enter an amount.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be at least $0.01.',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();

            return response()->json([
                'success' => false,
                'message' => $firstError,
                'errors' => $validator->errors()
            ], 422);
        }

        $senderId = $request->user()->id;
        $receiverId = $request->receiver_id;
        $amount = (float) $request->amount;

        if ($senderId === $receiverId) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot send money to yourself'
            ], 422);
        }

        $commissionFee = $amount * 0.015;
        $totalAmount = $amount + $commissionFee;

        try {
            return DB::transaction(function () use ($senderId, $receiverId, $amount, $commissionFee, $totalAmount, $request) {

                $sender = User::where('id', $senderId)->lockForUpdate()->first();
                $receiver = User::where('id', $receiverId)->lockForUpdate()->first();

                if ($sender->balance < $totalAmount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient balance'
                    ], 422);
                }

                $sender->decrement('balance', $totalAmount);
                $receiver->increment('balance', $amount);

                $sender->refresh();
                $receiver->refresh();

                $transaction = Transaction::create([
                    'sender_id' => $senderId,
                    'receiver_id' => $receiverId,
                    'amount' => $amount,
                    'commission_fee' => $commissionFee,
                    'type' => isset($request->type) ? $request->type : 'transfer',
                    'status' => 'completed',
                    'description' => $request->description,
                ]);

                $transaction->load(['sender:id,name,email', 'receiver:id,name,email']);

                broadcast(new TransactionCompleted(
                    $transaction,
                    (float) $sender->balance,
                    (float) $receiver->balance
                ));

                return response()->json([
                    'success' => true,
                    'message' => 'Transaction completed successfully',
                    'data' => [
                        'transaction' => [
                            'id' => $transaction->id,
                            'type' => $transaction->type,
                            'amount' => (float) $transaction->amount,
                            'commission_fee' => (float) $transaction->commission_fee,
                            'total_amount' => (float) $transaction->total_amount,
                            'status' => $transaction->status,
                            'sender' => [
                                'id' => $transaction->sender->id,
                                'name' => $transaction->sender->name,
                            ],
                            'receiver' => [
                                'id' => $transaction->receiver->id,
                                'name' => $transaction->receiver->name,
                            ],
                        ],
                        'current_balance' => (float) $sender->balance,
                    ]
                ], 201);
            });

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction failed'
            ], 500);
        }
    }
}