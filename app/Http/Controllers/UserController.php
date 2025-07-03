<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $users = User::all();
    return view('users.index', [
      "users" => $users,
    ]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\User  $user
   * @return \Illuminate\Http\Response
   */
  public function show(User $user)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\User  $user
   * @return \Illuminate\Http\Response
   */
  public function edit(User $user)
  {
    return view('users.edit', [
      'user' => $user,
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\User  $user
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, User $user)
  {
    try {
      Log::info("User update request received for user ID: {$user->id}");
      $rules = [];

      if ($request->name !== $user->name) {
        $rules['name'] = 'required|string|max:255';
      }
      if ($request->email !== $user->email) {
        $rules['email'] = 'required|email|max:255|unique:users,email';
      }
      if ($request->phone_number !== $user->phone_number) {
        $rules['phone_number'] = 'required|string|max:255|unique:users,phone_number';
      }
      if ($request->username !== $user->username) {
        $rules['username'] = 'nullable|string|max:255|unique:users,username';
      }

      if ($request->username !== $user->username) {
        $rules['username'] = 'nullable|string|max:255|unique:users,username';

        // Fetch Telegram API jika username berubah dan tidak kosong
        if ($request->username) {
          $token = env('TELEGRAM_BOT_TOKEN');
          $apiUrl = "https://api.telegram.org/bot{$token}/getUpdates";
          $response = Http::get($apiUrl);


          if ($response->ok()) {
            $result = $response->json('result');
            // dd($result['result']);
            $chatId = null;

            $result = $result['result'];
            foreach ($result as $item) {
              // dd($item);
              $found = $item['message']['chat']['username'] ??  null;
              if (!is_null($found) && $found === $request->username) {
                // dd($found);
                $chatId = $item['message']['chat']['id'] ?? null;
                // dd($chatId);

                Log::info("Telegram chat ID found: " . $chatId ?? 'not found');
                if ($chatId) {
                  $request->merge(['telegram_chat_id' => $chatId]);
                  $rules['telegram_chat_id'] = 'required|numeric';
                }
              }
              // if ($msg) {
              //   $fromUsername = isset($msg['from']['username']) ? strtolower($msg['from']['username']) : null;
              //   $chatUsername = isset($msg['chat']['username']) ? strtolower($msg['chat']['username']) : null;

              //   if ($fromUsername === $inputUsername || $chatUsername === $inputUsername) {
              //     $chatId = $msg['chat']['id'] ?? null;
              //     break;
              //   }
              // }
            }
          }
        }
      }
      $rules['is_admin'] = 'required|boolean';

      $validatedData = $request->validate($rules);
      // dd($validatedData);

      $user->update($validatedData);

      return redirect()->route('users.index')->with('success', 'User updated successfully.');
    } catch (\Exception $e) {
      Log::error("Error updating user: {$e->getMessage()}");
      return redirect()->back()->withErrors(['error' => 'Failed to update user.']);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\User  $user
   * @return \Illuminate\Http\Response
   */
  public function destroy(User $user)
  {
    //
  }

  public function apiIndex()
  {
    $users = User::all();

    return response()->json([
      'success' => true,
      'data' => $users,
    ]);
  }

  public function apiEdit(User $user)
  {
    return response()->json([
      'success' => true,
      'data' => $user,
    ]);
  }
}
