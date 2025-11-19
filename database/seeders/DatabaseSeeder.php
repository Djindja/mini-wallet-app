<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'balance' => 1000.00,
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'balance' => 500.00,
        ]);

        $user3 = User::create([
            'name' => 'Bob Wilson',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
            'balance' => 750.00,
        ]);

        Transaction::create([
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id,
            'amount' => 100.00,
            'commission_fee' => 1.50,
            'type' => 'payment',
            'status' => 'completed',
            'description' => 'Payment for lunch',
        ]);

        Transaction::create([
            'sender_id' => $user2->id,
            'receiver_id' => $user3->id,
            'amount' => 50.00,
            'commission_fee' => 0.75,  // 1.5% of 50
            'type' => 'transfer',
            'status' => 'completed',
            'description' => 'Book purchase',
        ]);

        echo "\n✅ Database seeded successfully!\n\n";
        echo "Test Users:\n";
        echo "1. Email: john@example.com | Password: password | Balance: $1000\n";
        echo "2. Email: jane@example.com | Password: password | Balance: $500\n";
        echo "3. Email: bob@example.com  | Password: password | Balance: $750\n\n";
    }
}
