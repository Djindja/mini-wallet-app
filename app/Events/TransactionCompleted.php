<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;
    public $senderBalance;
    public $receiverBalance;

    public function __construct(Transaction $transaction, float $senderBalance, float $receiverBalance)
    {
        $this->transaction = $transaction;
        $this->senderBalance = $senderBalance;
        $this->receiverBalance = $receiverBalance;
    }

    public function broadcastOn()
    {
        return new Channel('transactions');
    }
}