<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmail implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $for_email, $mailable;


  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(string $for_email, $mailable)
  {
    $this->for_email = $for_email;
    $this->mailable = $mailable;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    Mail::to($this->for_email)
      ->send($this->mailable);
  }
}
