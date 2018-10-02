<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\UserRegistered'         => ['App\Listeners\SendConfirmationEmail'],
        'App\Events\EmailConfirmed'         => ['App\Listeners\SendWelcomeEmail'],
        'App\Events\PasswordResetRequested' => ['App\Listeners\SendPasswordResetEmail'],
        'App\Events\PasswordChanged'        => ['App\Listeners\SendPasswordChangedEmail'],
        'App\Events\FollowerAdded'          => ['App\Listeners\SendFollowerNotificationEmail'],
        'App\Events\FollowerInvited'        => ['App\Listeners\SendFollowerInvitationEmail'],
        'App\Events\SocialActivityAdded'    => ['App\Listeners\StoreSocialActivity'],
        'App\Events\Gamification'           => ['App\Listeners\EarnAchievementListener','App\Listeners\SetPointsListener','App\Listeners\LogGamificationActionsListener']
    ];
}
