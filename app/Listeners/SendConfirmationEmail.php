<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Libraries\MandrillMailer;
use URL;

class SendConfirmationEmail
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

    public function handle(UserRegistered $event)
    {
        $confirmation_url = env('API_URL') . '/users/confirm/' . $event->token;
        $templateName = 'hackair-active-account';
        $templateContent = [];
        $message = [
            'subject' => 'Activate your account | hackAIR',
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
                            'name' => 'CONFIRMATION_URL',
                            'content' => $confirmation_url
                        ]
                    ]
                ]
            ]
        ];

        MandrillMailer::send($templateName, $templateContent, $message);
    }
}
