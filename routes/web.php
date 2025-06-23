<?php

use App\Mail\RequestApproveMail;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendEmail;
use App\Jobs\SendTelegram;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StockController;
use App\Notifications\RequestApprovalTelegram;
use App\Http\Controllers\RegistrationController;
use NotificationChannels\Telegram\TelegramUpdates;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::get('/registration', [RegistrationController::class, 'index']);
Route::post('/registration', [RegistrationController::class, 'store']);

Route::middleware('auth')->group(function () {
  // Route::get('/', [StockController::class, 'index'])->name('homepage');
  Route::resource('/stock', 'StockController');

  Route::get('/request-approval', 'RequestApprovalController@index')->name('request-approval.index');
  Route::get('/getRequestData', 'RequestApprovalController@getRequestData');
  Route::post('/requestApproval', 'RequestApprovalController@requestApproval');

  //accept approval
  Route::post('/request-approval/accept/{requestId}', 'RequestApprovalController@acceptApproval');
});

Route::get('/input', function () {
  return view('input');
});

Route::post('/input', function (Request $request) {
  // dd($request->all());
  try {
    // ambil telegram_id
    // $updates = TelegramUpdates::create()
    //   ->limit(2)
    //   ->options([
    //     'timeout' => 0,
    //   ])
    //   ->get();

    // if ($updates['ok']) {
    //   $chatId = $updates['result'][0]['message']['chat']['id'];
    //   Log::info('Telegram Chat ID: ' . $chatId);
    //   // Simpan $chatId ke database sesuai user aplikasi Anda
    //   // Misal: User::where('email', $email)->update(['telegram_user_id' => $chatId]);
    // }

    // if ($updates['ok'] && !empty($updates['result'])) {
    //     $chatId = $updates['result'][0]['message']['chat']['id'];
    //     // Lanjutkan proses
    // } else {
    //     // Tidak ada update baru
    //     return response()->json([
    //         'status' => 'error',
    //         'message' => 'Tidak ada update baru dari Telegram.'
    //     ]);
    // }

    // setup data
    $data = (object)[
      'email' => $request->email,
      // 'telegram_user_id' => $chatId ?? null,
      'text' => $request->text,
    ];

    // Log::info('User Information: ' . $data->telegram_chat_id);

    $mailable = new RequestApproveMail($data);
    $notified = new RequestApprovalTelegram($data);

    // Dispatch the job to send the email & telegram
    SendEmail::dispatch($request->email, $mailable)->onQueue('default');
    SendTelegram::dispatch($data)->onQueue('default');

    echo ('Data berhasil disimpan dan notifikasi telah dikirim.');
  } catch (\Exception $e) {
    return response()->json([
      'status' => 'error',
      'message' => $e->getMessage(),
    ]);
  }
});
