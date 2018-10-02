<?php

namespace App\Listeners;

use App\Events\FollowerAdded;
use App\Libraries\MandrillMailer;
use Illuminate\Support\Facades\Log;
use App\User;

class SendFollowerNotificationEmail
{
    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {
        //
    }

    public function handle(FollowerAdded $event)
    {
        $user = User::find($event->userId);

        if($user->notify_email == false)
            return false;

        $follower = User::find($event->followerId);
        $profileFollowersUrl = env('WEB_URL') . '/profile/followers';

        $templateName = 'hackair-new-follower';
        $templateContent = [];
        $message = [
            'subject' => 'You have a new follower in hackAIR!',
            'to' => array(
                array(
                    'email' => $user->email,
                    'name' => $user->getFullname(),
                    'type' => 'to'
                )
            ),
            'merge' => true,
            'merge_vars' => [
                [
                    'rcpt' =>  $user->email,
                    'vars' => [
                        [
                            'name' => 'FOLLOWER_NAME',
                            'content' => $follower->getFullname()
                        ],
                        [
                            'name' => 'PROFILE_FOLLOWERS_URL',
                            'content' => $profileFollowersUrl
                        ]
                    ]
                ]
            ]
        ];

        MandrillMailer::send($templateName, $templateContent, $message);
        Log::info('New follower email sent.',['recipient' => $user->email]);
    }
}
