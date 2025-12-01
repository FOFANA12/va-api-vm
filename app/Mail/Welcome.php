<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Welcome extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $pass;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct(User $user, $pass)
    {
        $this->user = $user;
        $this->pass = $pass;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $locale = config('app.locale', 'fr');
        $markdownView = "mail.$locale.welcome";

        return $this->from(config('mail.from.address'), config('app.name'))
            ->subject(__('app/mail/welcome_subject', ['name' => config('app.name')]))
            ->markdown($markdownView)
            ->with([
                'user' => $this->user,
                'pass' => $this->pass,
            ]);
    }
}
