<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $locale = config('app.locale', 'fr');
        $markdownView = "mail.$locale.reset_password";
        
        return $this->from(config('mail.from.address'), config('app.name'))
            ->subject(__('app/mail.reset_password_subject'))
            ->markdown($markdownView)
            ->with([
                'user' => $this->user,
                'token' => $this->token,
            ]);
    }
}
