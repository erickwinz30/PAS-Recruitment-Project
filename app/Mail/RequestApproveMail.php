<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestApproveMail extends Mailable
{
  use Queueable, SerializesModels;

  protected $data;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($data)
  {
    $this->data = $data;
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

    return $this->from('your@email.com', 'Nama Pengirim')
      ->subject('Permintaan Approval Baru')
      ->view('mail.result')
      ->with([
        'email' => $this->data->email,
        'text' => $this->data->text,
      ]);
  }
}
