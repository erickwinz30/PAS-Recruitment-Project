<?php

namespace App\Notifiables;

use Illuminate\Notifications\Notifiable;

class TelegramNotifiable
{
  use Notifiable;

  public $telegram_chat_id;

  public function __construct($telegram_chat_id)
  {
    $this->telegram_chat_id = $telegram_chat_id;
  }

  public function routeNotificationForTelegram()
  {
    return $this->telegram_chat_id;
  }
}
