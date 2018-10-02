<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Dingo\Api\Facade\API;

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\v1'], function ($api) {

    $api->get('/', function () use ($api) {
        return 'hackAIR API v1';
    });

    $api->group(['prefix' => 'map', 'middleware' => ['cors']], function () use ($api) {
        $api->get('/', 'MapController@getMap');
    });

    $api->group(['prefix' => 'aq', 'middleware' => ['cors']], function () use ($api) {
        $api->get('/', 'AirQualityController@getAQ');
    });

    $api->group(['prefix' => 'users', 'middleware' => ['cors']], function () use ($api) {

        $api->post('/', 'AuthController@register');
        $api->post('/login', 'AuthController@login');
        $api->get('/token', 'AuthController@refreshToken');
        $api->get('/confirm/{confirmation_token}', 'AuthController@confirmEmail');
        $api->post('/password/reset', 'UserController@sendResetPasswordEmail');
        $api->post('/password/change', 'UserController@resetPassword');
        $api->get('/unsubscribe/{unsubscribe_token}', 'UserController@unsubscribe');

    });

    $api->group(['prefix' => 'reset-password', 'middleware' => ['cors']], function () use ($api) {
        $api->get('/{reset_password_code}', 'UserController@showPasswordResetView');
        $api->get('/success/{success}', 'UserController@showResetPassSuccess');
    });

    $api->group(['prefix' => 'users', 'middleware' => ['cors', 'auth:api']], function () use ($api) {
        // TODO add role-based authentication before enabling this route
        // $api->get('/', 'UserController@index');
        $api->post('/social/search', 'UserController@searchUser');
        $api->get('/social/feed', 'UserController@socialFeed');
        $api->put('/social', 'UserController@toggleAllSocialActivitySwitch');
        $api->put('/social/{user_activity_id}', 'UserController@toggleSocialActivitySwitch');
        $api->get('/social/communities', 'UserController@socialCommunities');
        $api->get('/social/my-communities', 'UserController@mySocialCommunities');
        $api->post('/social/communities/{social_community_id}', 'UserController@joinSocialCommunity');
        $api->delete('/social/communities/{social_community_id}', 'UserController@leaveSocialCommunity');
        $api->get('/recommendations', 'UserController@getRecommendations');

        $api->get('/{user_id}', 'UserController@show');
        $api->put('/{user_id}', 'UserController@update');
        $api->put('/{user_id}/privacy', 'UserController@togglePrivate');
        $api->delete('/{user_id}', 'UserController@destroy');
        $api->post('/{user_id}/profile_picture', 'UserController@UpdateProfilePicture');
        $api->post('/{user_id}/password', 'UserController@updatePassword');
        $api->put('/following/{user_id}', 'UserController@follow');
        $api->delete('/following/{user_id}', 'UserController@unfollow');
        $api->post('/invite', 'UserController@inviteFollower');
        $api->post('/stop_mails', 'UserController@stopEmails');
        $api->post('/accept_newsletters', 'UserController@accept_newsletters');


    });

    $api->group(['prefix' => 'sensors', 'middleware' => ['cors', 'auth:api']], function () use ($api) {
        $api->get('/', 'SensorController@index');
        $api->get('/{sensor_id}', 'SensorController@show');
        $api->post('/', 'SensorController@register');
        $api->put('/{sensor_id}', 'SensorController@update');
        $api->put('/{sensor_id}/access_key', 'SensorController@refreshAccessKey');
        $api->delete('/{sensor_id}', 'SensorController@destroy');
        $api->post('/bleair/measurements', 'SensorController@bleairMeasurement');
        //$api->get('/cots/measurements','PhotoCOTSController@getCotsMeasurements');
    });

    $api->group(['prefix' => 'sensors', 'middleware' => ['cors', 'auth.arduino']], function () use ($api) {
        $api->post('/arduino/measurements', 'SensorController@arduinoMeasurement');
        $api->post('/push/measurements', 'SensorController@arduinoMeasurement');
    });

    $api->group(['prefix' => 'measurements', 'middleware' => ['cors']], function () use ($api) {

        $api->get('/', 'MeasurementController@search');
        $api->post('/', 'MeasurementController@create');
        $api->put('/', 'MeasurementController@update');
        $api->delete('/', 'MeasurementController@delete');
        $api->get('/export', 'MeasurementController@export');
    });

    $api->group(['prefix' => 'hackair_data', 'middleware' => ['cors']], function () use ($api) {

        $api->get('/', 'MeasurementController@getPublicAPIData');

    });

    $api->group(['prefix' => 'photos', 'middleware' => ['cors', 'auth:api']], function () use ($api) {
        $api->get('/', 'PhotoController@index');
        $api->get('/all','PhotoController@index');
        $api->get('/aqi/{imageId}','PhotoController@getAQI');
        $api->post('/flickr', 'PhotoFlickrController@create');
        $api->post('/sky', 'PhotoSkyController@create');
        $api->post('/sensor', 'PhotoCOTSController@create');
        $api->delete('/{photo_id}', 'PhotoController@destroy');
    });

    $api->group(['prefix' => 'levels', 'middleware' => ['cors', 'auth:api']], function () use ($api) {
        $api->get('/', 'LevelController@index');
    });

    $api->group(['prefix' => 'achievements', 'middleware' => ['cors', 'auth:api']], function () use ($api) {
        $api->get('/', 'AchievementController@index');
    });

    $api->group(['prefix' => 'missions', 'middleware' => ['cors', 'auth:api']], function () use ($api) {
        $api->get('/', 'MissionController@index');
        $api->get('/{mission_id}', 'MissionController@show');
    });

    $api->group(['prefix' => 'misc'], function () use ($api) {
        $api->get('/newsletter/send', 'NewsletterController@send');
        $api->get('/newsletter/test/{user_id}', 'NewsletterController@test');
        $api->get('/newsletter/view/{user_id}/{date}/{token}', 'NewsletterController@view');
        $api->get('/thumb', 'PhotoController@thumb');
    });

    $api->group(['prefix' => 'perceptions', 'middleware' => ['cors']], function () use ($api) {
        $api->get('/', 'PerceptionController@index');
        $api->post('/', 'PerceptionController@create');
    });

    $api->group(['prefix' => 'social', 'middleware' => ['cors','auth:api']], function() use ($api) {
        $api->get('/feed', 'SocialController@feed');

        /**
         * Routes for social community resource
         */
        $api->get('communities', 'SocialCommunityController@index');
        $api->get('communities/{id}', 'SocialCommunityController@show');
        $api->post('communities', 'SocialCommunityController@store');
        $api->put('communities/{id}', 'SocialCommunityController@update');
        $api->delete('communities/{id}', 'SocialCommunityController@destroy');
        $api->get('communities/{id}/feed', 'SocialCommunityController@feed');
        $api->get('communities/{id}/members', 'SocialCommunityController@getMembers');
    });


    $api->group(['prefix' => 'content', 'middleware' => ['cors','auth:api']], function() use ($api) {
        $api->get('/recommendations', 'ContentController@getRecommendationsContent');

    });
    
    $api->group(['prefix' => 'forum', 'middleware' => ['cors']], function () use ($api) {
        /**
         * Routes for resource tag
         */
        $api->get('tags', 'ForumTagController@index');
        $api->get('tags/{id}', 'ForumTagController@show');
        $api->post('tags', 'ForumTagController@store');
        $api->put('tags/{id}', 'ForumTagController@update');
        $api->delete('tags/{id}', 'ForumTagController@destroy');

        /**
         * Routes for resource thread
         */
        $api->get('threads', 'ForumThreadController@index');
        $api->get('threads/{id}', 'ForumThreadController@show');
        $api->post('threads', 'ForumThreadController@store');
        $api->put('threads/{id}', 'ForumThreadController@update');
        $api->delete('threads/{id}', 'ForumThreadController@destroy');

        /**
         * Routes for resource reply
         */
        $api->get('replies', 'ForumReplyController@index');
        $api->get('replies/{id}', 'ForumReplyController@show');
        $api->post('replies', 'ForumReplyController@store');
        $api->put('replies/{id}', 'ForumReplyController@update');
        $api->delete('replies/{id}', 'ForumReplyController@destroy');
    });

    // -- Legacy routes

    $api->group(['prefix' => 'v1/sensors', 'middleware' => ['auth.arduino']], function () use ($api) {
        $api->post('/arduino/measurements', 'SensorController@arduinoMeasurement');
    });

    // !-- Legacy routes
});
