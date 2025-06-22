<?php

namespace App\Http\Controllers;

use App\Stock;
use App\RequestApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RequestApprovalController extends Controller
{
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

      // Create a new request approval
      $requestApproval = RequestApproval::create($validatedData);

      Log::info('Request approval created successfully: ', $requestApproval->toArray());

      return response()->json([
        'success' => true,
        'message' => 'Stock masuk/keluar berhasil direquest! Tunggu approval dari admin.',
        // 'data' => $requestApproval,
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
}
