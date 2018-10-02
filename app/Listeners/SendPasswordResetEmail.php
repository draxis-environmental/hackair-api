<?php

namespace App\Listeners;

use App\Events\PasswordResetRequested;
use App\Libraries\MandrillMailer;
use URL;

class SendPasswordResetEmail
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

    public function handle(PasswordResetRequested $event)
    {
        $reset_url = env('API_URL') . '/reset-password/' . $event->token;
        $templateName = 'hackair-password-reset';
        $templateContent = [];
        $message = [
            'subject' => 'Reset your password | hackAIR',
            'to' => array(
                array(
                    'email' => $event->user->email,
                    'name' => $event->user->name . ' ' . $event->user->surname,
                    'type' => 'to'
                )
            ),
            'merge' => true,
            'merge_vars' => [
                [
                    'rcpt' =>  $event->user->email,
                    'vars' => [
                        [
                            'name' => 'RESET_URL',
                            'content' => $reset_url
                        ]
                    ]
                ]
            ]
        ];

        MandrillMailer::send($templateName, $templateContent, $message);
    }
}
