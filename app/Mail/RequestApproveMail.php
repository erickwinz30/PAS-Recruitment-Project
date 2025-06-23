<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestApproveMail extends Mailable
{
  use Queueable, SerializesModels;

  protected $notificationData;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct(Object $notificationData)
  {
    $this->notificationData = $notificationData;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    // return $this->view('mail.result', [
    //   'email' => $this->data->email,
    //   'text' => $this->data->text,
    // ]);

    $is_entry = $this->notificationData->is_entry ? 'Masuk' : 'Keluar';

    return $this->from('pas_@email.com', 'Sistem PAS')
      ->subject('Permintaan Persetujuan Barang ' . $is_entry . ' untuk ' . $this->notificationData->stock_name)
      ->view('mail.result')
      ->with([
        'text' => "Permintaan persetujuan dari " . $this->notificationData->user . " dengan informasi lebih lengkap:<br>" .
          "Barang       : " . $this->notificationData->stock_name . "<br>" .
          "Jumlah       : " . $this->notificationData->amount . "<br>" .
          "Masuk/Keluar : " . $is_entry . "<br><br>" .
          "Silahkan melakukan konfirmasi persetujuan melalui aplikasi.",
      ]);
  }
}
