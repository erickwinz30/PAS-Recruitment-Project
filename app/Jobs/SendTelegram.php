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

  private string $telegram_chat_id;
  private object $notificationData;


  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(string $telegram_chat_id, Object $notificationData)
  {
    $this->telegram_chat_id = $telegram_chat_id;
    $this->notificationData = $notificationData;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    try {
      // $user = User::whereNotNull('telegram_chat_id')
      //   ->where('telegram_chat_id', '!=', '')
      //   ->first();

      $notifiable = new TelegramNotifiable($this->telegram_chat_id);
      $notifiable->notify(new RequestApprovalTelegram($this->notificationData));

      Log::info('Telegram notification sent successfully to chat ID: ' . $this->telegram_chat_id);
    } catch (\Exception $e) {
      Log::error('Error sending Telegram notification: ' . $e->getMessage());
    }
  }
}
