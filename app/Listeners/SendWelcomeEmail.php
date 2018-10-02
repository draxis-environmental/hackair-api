<?php

namespace App\Listeners;

use App\Events\EmailConfirmed;
use App\Libraries\MandrillMailer;

class SendWelcomeEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle(EmailConfirmed $event)
    {
        $templateName = 'hackair-welcome-email';
        $templateContent = [];
        $message = [
            'subject' => 'Welcome to hackAIR!',
            'to' => array(
                array(
                    'email' => $event->user->email,
                    'name' => $event->user->name . ' ' . $event->user->surname,
                    'type' => 'to'
                )
            )
        ];

        MandrillMailer::send($templateName, $templateContent, $message);
    }
}
