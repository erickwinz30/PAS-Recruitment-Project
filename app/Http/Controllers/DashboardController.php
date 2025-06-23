<?php

namespace App\Http\Controllers;

use App\RequestApproval;
use App\Stock;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $data = $this->fetch($request);
    return view('dashboard', $data);
  }

  // Mengambil data dashboard
  public function fetch(Request $request)
  {
    $data = [];
    // Get semua data pada stock untuk pie chart
    $data['stocks'] = RequestApproval::where('status', 'approved')
      ->with('stock:id,name')
      ->groupBy('stock_id');
    // Get 10 aktivitas terbaru dari approval stock
    $data['activities'] = RequestApproval::limit(10)->orderBy('updated_at', 'DESC')->with('stock')->get();
    // Get stock untuk widget
    $data['widget'] = [];
    $data['widget']['total_stock'] = RequestApproval::where('status', 'approved');
    $data['widget']['total_stock_in'] = RequestApproval::where('status', 'approved')->where('is_entry', true);
    $data['widget']['total_stock_out'] = RequestApproval::where('status', 'approved')->where('is_entry', false);
    // Cek jika ada query string 'month' dan 'year'
    if ($request->has('month') && $request->has('year')) {
      $month = $request->query('month');
      $year = $request->query('year');
      // Filter data berdasarkan tanggal
      $data['stocks'] = $data['stocks']->whereMonth('updated_at', $month)->whereYear('updated_at', $year)->selectRaw('stock_id, SUM(amount) as amount_sum')->get();
      $data['widget']['total_stock'] = $data['widget']['total_stock']->whereMonth('updated_at', $month)->whereYear('updated_at', $year)->sum('amount');
      $data['widget']['total_stock_in'] = $data['widget']['total_stock_in']->whereMonth('updated_at', $month)->whereYear('updated_at', $year)->sum('amount');
      $data['widget']['total_stock_out'] = $data['widget']['total_stock_out']->whereMonth('updated_at', $month)->whereYear('updated_at', $year)->sum('amount');
    } else {
      $data['stocks'] = $data['stocks']->selectRaw('stock_id, SUM(amount) as amount_sum')->get();
      $data['widget']['total_stock'] = $data['widget']['total_stock']->sum('amount');
      $data['widget']['total_stock_in'] = $data['widget']['total_stock_in']->sum('amount');
      $data['widget']['total_stock_out'] = $data['widget']['total_stock_out']->sum('amount');
    }
    return $data;
  }
}
