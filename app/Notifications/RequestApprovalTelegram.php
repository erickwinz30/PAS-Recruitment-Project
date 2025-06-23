<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;


class RequestApprovalTelegram extends Notification
{
  use Queueable;

  protected object $notificationData;


  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct(Object $notificationData)
  {
    $this->notificationData = $notificationData;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via(object $notificationData)
  {
    return ['telegram'];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toTelegram($notifiable)
  {
    $is_entry = $this->notificationData->is_entry ? 'Masuk' : 'Keluar';

    $text = "Permintaan persetujuan dari " . $this->notificationData->user . " dengan informasi lebih lengkap:\n" .
      "Barang       : " . $this->notificationData->stock_name . "\n" .
      "Jumlah       : " . $this->notificationData->amount . "\n" .
      "Masuk/Keluar : " . $is_entry . "\n\n" .
      "Silahkan melakukan konfirmasi persetujuan melalui aplikasi.";

    return TelegramMessage::create()
      ->to($notifiable->telegram_chat_id)
      ->content($text);
  }

  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
      //
    ];
  }
}
