<?php

namespace App\Http\Controllers;

use App\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class StockController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $stocks = Stock::where('is_deleted', false)
      ->orderBy('created_at', 'desc')
      ->get();

    return view('index', [
      'stocks' => $stocks,
    ]);
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
    try {
      // Log::info('Request Data: ', $request->all());

      $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'amount' => 'required|integer|min:0',
      ]);

      Stock::create($validatedData);
      Log::info('Stock created successfully: ', $validatedData);

      if ($request->ajax()) {
        return response()->json(['success' => true, 'message' => 'Stock baru berhasil ditambahkan!.']);
      }

      // return redirect('/stock')->with('success', 'Stock created successfully.');
    } catch (\Exception $e) {
      Log::error('Error storing stock: ' . $e->getMessage());

      if ($request->ajax()) {
        $message = 'Gagal menambahkan stock baru.';
        $errors = [];

        // Jika error validasi, ambil pesan error validasi
        if ($e instanceof \Illuminate\Validation\ValidationException) {
          $message = 'Validasi gagal!';
          $errors = $e->errors();
        }

        return response()->json([
          'success' => false,
          'message' => $message,
          'errors' => $errors
        ], 422);
      }

      // Untuk request biasa, redirect back dengan pesan error
      return redirect()->back()->with('error', 'Gagal menambahkan stock baru.');
      // return redirect()->back()->with('error', 'Failed to store stock.');
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(stock $stock)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(stock $stock)
  {
    Log::info('Trying to edit stock: ', ['id' => $stock->id, 'name' => $stock->name, 'amount' => $stock->amount]);

    return response()->json([
      'id' => $stock->id,
      'name' => $stock->name,
      'amount' => $stock->amount,
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, stock $stock)
  {
    try {
      // Log::info('Trying Updating stock: ', ['id' => $stock->id]);
      // dd($request->all());

      if ($request->name !== $stock->name) {
        $rules['name'] = 'required|string|max:255';
      }

      if ($request->amount !== $stock->amount) {
        $rules['amount'] = 'required|integer|min:0';
      }

      $validatedData = $request->validate($rules ?? []);

      $stock->update($validatedData);
      Log::info('Stock updated successfully: ', $validatedData);

      if ($request->ajax()) {
        return response()->json(['success' => true, 'message' => 'Stock berhasil diupdate!.']);
      }
    } catch (\Exception $e) {
      Log::error('Error updating stock: ' . $e->getMessage());

      if ($request->ajax()) {
        return response()->json(['success' => false, 'message' => 'Gagal memperbarui stock.'], 422);
      }

      return redirect()->back()->with('error', 'Gagal memperbarui stock.');
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Request $request, stock $stock)
  {
    try {
      Log::info('Trying to delete stock: ', ['id' => $stock->id, 'name' => $stock->name, 'amount' => $stock->amount]);

      $stock->update(['is_deleted' => true]);
      Log::info('Stock deleted successfully: ', ['id' => $stock->id]);

      if ($request->ajax()) {
        return response()->json(['success' => true, 'message' => 'Stock berhasil dihapus!']);
      }

      return response()->json(['success' => true, 'message' => 'Stock berhasil dihapus!']);
    } catch (\Exception $e) {
      Log::error('Error deleting stock: ' . $e->getMessage());

      return response()->json(['success' => false, 'message' => 'Gagal menghapus stock.'], 422);
    }
  }

  public function apiIndex()
  {
    $stocks = Stock::where('is_deleted', false)
      ->orderBy('created_at', 'desc')
      ->get();

    return response()->json([
      'success' => true,
      'data' => $stocks,
    ]);
  }
}
