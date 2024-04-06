<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{

    use Queueable;

    public $token;
    public $email;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(config('name') . '-' . __('auth.reset_mail.subject'))
            ->line(__('auth.reset_mail.line1'))
            ->action(__('auth.reset_mail.subject'), env('APP_FRONT_URL')."/reset/" . $this->token . "?email=" . $this->email)
            ->line(__('auth.reset_mail.line2'));
    }
}
