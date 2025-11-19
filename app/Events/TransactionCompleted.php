<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;
    public $senderBalance;
    public $receiverBalance;

    /**
     * Create a new event instance.
     */
    public function __construct(Transaction $transaction, float $senderBalance, float $receiverBalance)
    {
        $this->transaction = $transaction;
        $this->senderBalance = $senderBalance;
        $this->receiverBalance = $receiverBalance;
    }

    /**
     * Get the channels the event should broadcast on.
     * Broadcast to both sender and receiver private channels
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->transaction->sender_id),
            new PrivateChannel('user.' . $this->transaction->receiver_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'transaction.completed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'transaction' => [
                'id' => $this->transaction->id,
                'sender_id' => $this->transaction->sender_id,
                'receiver_id' => $this->transaction->receiver_id,
                'amount' => (float) $this->transaction->amount,
                'commission_fee' => (float) $this->transaction->commission_fee,
                'total_amount' => (float) $this->transaction->total_amount,
                'type' => $this->transaction->type,
                'status' => $this->transaction->status,
                'description' => $this->transaction->description,
                'sender' => [
                    'id' => $this->transaction->sender->id,
                    'name' => $this->transaction->sender->name,
                    'email' => $this->transaction->sender->email,
                ],
                'receiver' => [
                    'id' => $this->transaction->receiver->id,
                    'name' => $this->transaction->receiver->name,
                    'email' => $this->transaction->receiver->email,
                ],
                'timestamp' => $this->transaction->created_at->toIso8601String(),
            ],
            'sender_balance' => $this->senderBalance,
            'receiver_balance' => $this->receiverBalance,
        ];
    }
}