<?php

namespace App\Jobs;

use App\User;
use Illuminate\Support\Facades\Log;
use App\Notifiables\TelegramNotifiable;
use App\Notifications\RequestApprovalTelegram;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTelegram implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private object $data;


  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(Object $data)
  {
    $this->data = $data;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    try {
      $user = User::whereNotNull('telegram_chat_id')
        ->where('telegram_chat_id', '!=', '')
        ->first();

      $notifiable = new TelegramNotifiable($user->telegram_chat_id);
      $notifiable->notify(new RequestApprovalTelegram($this->data));
    } catch (\Exception $e) {
      Log::error('Error sending Telegram notification: ' . $e->getMessage());
    }
  }
}
