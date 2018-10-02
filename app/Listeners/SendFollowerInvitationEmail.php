<?php

namespace App\Listeners;

use App\Events\FollowerInvited;
use App\Libraries\MandrillMailer;
use URL;
use Illuminate\Support\Facades\Hash;

class SendFollowerInvitationEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function handle(FollowerInvited $event)
    {
        $user = $event->user;
        $email = $event->email;

        // create a unique referral registration link
        $registrationUrl = env('WEB_URL') . '/register?ref=' . $user->affiliate_id;
        $referrerFullname = $user->getFullname();

        $templateName = 'hackair-follower-invitation';
        $templateContent = [];
        $message = [
            'subject' => 'You were invited to hackAir!',
            'to' => array(
                array(
                    'email' => $email,
                )
            ),
            'merge' => true,
            'merge_vars' => [
                [
                    'rcpt' =>  $email,
                    'vars' => [
                        [
                            'name' => 'REFERRER_USERNAME',
                            'content' => $referrerFullname
                        ],
                        [
                            'name' => 'REGISTRATION_URL',
                            'content' => $registrationUrl
                        ]
                    ]
                ]
            ]
        ];

        MandrillMailer::send($templateName, $templateContent, $message);
    }
}
