<?php

namespace App\Http\Controllers;

use App\User;
use App\Stock;
use App\RequestApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendEmail;
use App\Jobs\SendTelegram;
use App\Mail\RequestApproveMail;
use App\Notifications\RequestApprovalTelegram;



class RequestApprovalController extends Controller
{
  public function index()
  {
    $requestApprovals = RequestApproval::where('status', 'pending')->with(['user', 'stock'])->get();
    Log::info('Fetching request approvals: ', $requestApprovals->toArray());

    return view('request.index', compact('requestApprovals'));
  }

  public function requestApproved()
  {
    $requestApprovals = RequestApproval::where('status', 'approved')->with(['user', 'stock'])->get();
    Log::info('Fetching approved request approvals: ', $requestApprovals->toArray());

    return view('request.index', compact('requestApprovals'));
  }

  public function requestRejected()
  {
    $requestApprovals = RequestApproval::where('status', 'rejected')->with(['user', 'stock'])->get();
    Log::info('Fetching rejected request approvals: ', $requestApprovals->toArray());

    return view('request.index', compact('requestApprovals'));
  }

  public function allRequest()
  {
    $requestApprovals = RequestApproval::with(['user', 'stock'])->get();
    Log::info('Fetching all request approvals: ', $requestApprovals->toArray());

    return view('request.index', compact('requestApprovals'));
  }

  public function getRequestData(Request $request)
  {
    try {
      $stock = Stock::where('id', $request->id)->first();
      Log::info('Trying to request entry/outgoing stock: ', $request->all());

      return response()->json([
        'success' => true,
        'message' => 'Data stock berhasil ditemukan.',
        'id' => $stock->id,
        'name' => $stock->name,
      ]);
    } catch (\Exception $e) {
      Log::error('Error fetching request data: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Gagal mengambil data stock.',
      ], 500);
    }
  }
  public function requestApproval(Request $request)
  {
    try {
      Log::info('Request Approval Data: ', $request->all());

      $validatedData = $request->validate([
        'stock_id' => 'required|exists:stocks,id',
        'amount' => 'required|integer|min:1',
        'is_entry' => 'required|boolean',
      ]);

      $validatedData['user_id'] = Auth::user()->id; // Assuming you have user authentication in place

      $requestApproval = RequestApproval::create($validatedData);
      Log::info('Request approval created successfully: ', $requestApproval->toArray());

      //add notification for admin
      $notificationData = (object) [
        'stock_name' => $requestApproval->stock->name,
        'user' => $requestApproval->user->name,
        'is_entry' => $validatedData['is_entry'] ? 'Masuk' : 'Keluar',
        'amount' => $validatedData['amount'],
      ];

      Log::info('Notification Data: ', (array) $notificationData);

      //get all admin email and telegram chat id
      $admins = User::select('email', 'telegram_chat_id')->whereNotNull('email')->where('is_admin', true)->get();

      //sending email to admin
      $mailable = new RequestApproveMail($notificationData);
      foreach ($admins as $admin) {
        SendEmail::dispatch($admin->email, $mailable)->onQueue('default');
      }

      // $notified = new RequestApprovalTelegram($notificationData);
      // Dispatch the job to send the email & telegram
      // SendTelegram::dispatch($notificationData)->onQueue('default');

      return response()->json([
        'success' => true,
        'message' => 'Stock masuk/keluar berhasil direquest! Tunggu approval dari admin.',
      ]);
    } catch (\Exception $e) {
      Log::error('Error processing request approval: ' . $e->getMessage());
      if ($request->ajax()) {
        return response()->json([
          'success' => false,
          'message' => 'Gagal memproses request approval.',
        ], 500);
      }
    }
  }

  public function acceptApproval(string $requestId)
  {
    try {
      Log::info('Accepting request approval: ', ['requestId' => $requestId]);

      $requestApproval = RequestApproval::findOrFail($requestId);
      $requestApproval->status = 'approved';
      $requestApproval->save();

      Log::info('Request approval accepted successfully: ', $requestApproval->toArray());

      $currentAmount = $requestApproval->amount;
      $stock = Stock::findOrFail($requestApproval->stock->id);

      if ($requestApproval->is_entry) {
        $addedAmount = $stock->amount += $currentAmount;
        Log::info('Stock amount increased: ', ['stockId' => $stock->id, 'amount' => $addedAmount]);
      } else {
        if ($stock->amount < $currentAmount) {
          return response()->json([
            'success' => false,
            'message' => 'Stock tidak cukup untuk permintaan ini.',
          ], 400);
        }
        $decreasedAmount = $stock->amount -= $currentAmount;
        Log::info('Stock amount decreased: ', ['stockId' => $stock->id, 'amount' => $decreasedAmount]);
      }
      $stock->save();

      return response()->json([
        'success' => true,
        'message' => 'Request approval berhasil diterima dan telah diproses.',
      ]);
    } catch (\Exception $e) {
      Log::error('Error accepting request approval: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Gagal menerima request approval.',
      ], 500);
    }
  }

  public function rejectApproval(string $requestId)
  {
    try {
      Log::info('Rejecting request: ', ['requestId' => $requestId]);

      $requestApproval = RequestApproval::findOrFail($requestId);
      $requestApproval->status = 'rejected';
      $requestApproval->save();

      Log::info('Request approval rejected successfully: ', $requestApproval->toArray());

      return response()->json([
        'success' => true,
        'message' => 'Request approval berhasil ditolak.',
      ]);
    } catch (\Exception $e) {
      Log::error('Error rejecting request approval: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Gagal menolak request approval.',
      ], 500);
    }
  }
}
