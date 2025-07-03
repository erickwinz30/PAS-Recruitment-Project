<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Stock;
use App\RequestApproval;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    // $this->call(UsersTableSeeder::class);

    $admin =  User::create([
      'name' => 'Admin',
      'email' => 'admin@gmail.com',
      'phone_number' => '+6289666020017',
      'telegram_chat_id' => "856041698",
      'is_admin' => true,
      'is_deleted' => false,
      'password' => bcrypt('admin123'),
    ]);

    User::create([
      'name' => 'User',
      'email' => 'user@gmail.com',
      'phone_number' => '+6285155431948',
      'is_admin' => false,
      'is_deleted' => false,
      'password' => bcrypt('user123'),
    ]);

    $stocks = [
      [
        "name" => 'Mie Sedap Goreng',
        "amount" => 100,
      ],
      [
        'name' => 'Mie Sedap Kari Ayam',
        'amount' => 200,
      ],
      [
        'name' => 'Mie Sedap Kaldu Ayam',
        'amount' => 150,
      ],
      [
        'name' => 'Mie Sedap Goreng Jumbo',
        'amount' => 150,
      ],
      [
        'name' => 'Mie Sedap Korean Ramyeon',
        'amount' => 150,
      ]
    ];
    foreach ($stocks as $stock) {
      $entry_data = Stock::create([
        'name' => $stock['name'],
        'amount' => $stock['amount'],
      ]);
      // Menambahkan data untuk request_approvals
      $request_approval = RequestApproval::create(
        [
          'user_id' => $admin->id,
          'stock_id' => $entry_data->id,
          'status' => 'approved',
          'is_entry' => true,
          'amount' => $stock['amount'],
        ]
      );
      Log::info("Seeding Request Approval: " . $request_approval);
    }
  }
}
