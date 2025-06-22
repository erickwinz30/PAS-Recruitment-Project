<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Stock;

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

    User::create([
      'name' => 'Admin',
      'email' => 'admin@gmail.com',
      'phone_number' => '+6289666020017',
      'telegram_chat_id' => "856041698",
      'is_admin' => true,
      'is_deleted' => false,
      'password' => bcrypt('admin123'),
    ]);

    Stock::create([
      'name' => 'Mie Sedap Goreng',
      'amount' => 100,
    ]);

    Stock::create([
      'name' => 'Mie Sedap Kari Ayam',
      'amount' => 200,
    ]);
  }
}
