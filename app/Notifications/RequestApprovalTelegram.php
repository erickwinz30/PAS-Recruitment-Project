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

  protected object $data;


  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct(Object $data)
  {
    $this->data = $data;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via(object $data)
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
    return TelegramMessage::create()
      // Optional recipient user id.
      ->to($notifiable->telegram_chat_id)
      // ->to($data->phone_number)

      // Markdown supported.
      ->content(
        "Hello there! Your text is: " . $this->data->text . "\n" .
          "Your invoice has been *PAID*\n" .
          "Thank you!"
      );
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
