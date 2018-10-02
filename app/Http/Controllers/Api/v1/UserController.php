<?php

namespace App\Http\Controllers\Api\v1;

use App\Achievement;
use App\AdditionalProfile;
use App\Events\PasswordResetRequested;
use App\Events\FollowerAdded;
use App\Events\FollowerInvited;
use App\Events\Gamification;
use App\Events\SocialActivityAdded;
use App\Libraries\Recommendations;
use App\SocialActivity;
use App\SocialCommunity;
use App\UserSocialActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\User;
use App\UserActivation;
use App\Level;
use Event;
use App\Libraries\Responder;
use App\Libraries\GeoResultParser;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Log;


class UserController extends Controller
{
    /**
     * Retrieve all users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        foreach($users as &$user) {
            $user = $this->prepareRow($user);
        }

        return Responder::SuccessResponse( $users );
    }

    /**
     * Retrieve the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        // if the user exists
        if ($user) {
            $isMe = $id == Auth::id();
            // if its me and his profile is not private
            if ($isMe || !$user->private) {
                $user = $this->prepareRow($user);
                // Get location fields using Google API
                if (empty($user['place_id']) == false) {
                    $language = app('translator')->getLocale();
                    $fallbackLanguage = app('translator')->getFallBack();
                    if ($language != $fallbackLanguage) {
                        $locationFields = $this->getLocationFields($user['place_id'], $language);
                        $user['place'] = $locationFields['place'];
                        $user['location_str'] = $locationFields['location_str'];
                    }
                }
                return Responder::SuccessResponse($user);
            } else {
                return Responder::NotFoundError(); // profile is private
            }
        } else {
            return Responder::NotFoundError(); // user doesn't exist
        }
    }

    /**
     * Update the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            if (Auth::id() != $id) {
                return Responder::UnauthorizedError();
            }

            $user = User::find($id);

            if (empty($user)) {
                return Responder::NotFoundError( $user );
            }

            $payload = $request->all();

            if (array_key_exists('groups', $payload)) {
                $groups = $payload['groups'];
                unset($payload['groups']);
            }
            if (array_key_exists('outdoor_activities', $payload)) {
                $outdoor_activities = $payload['outdoor_activities'];
                unset($payload['outdoor_activities']);
            }

            if (array_key_exists('secondary_profile', $payload)) {
                $secondary_profile = $payload['secondary_profile'];
                unset($payload['secondary_profile']);


                if($secondary_profile['show'] == true ) {

                    $validator = \Validator::make($secondary_profile['details'], [
                        'year_of_birth' => 'required|numeric',
                        'selectedSensitivities' => 'required|array',
                        'selectedActivities' => 'required|array',
                    ]);

                    if ($validator->fails())
                        return Responder::ValidationError(Lang::get('profile.additional_profile'));

                    $additionalProfile = AdditionalProfile::firstOrNew(array('user_id' => $user->id));
                    $additionalProfile->user_id = $user->id;
                    $additionalProfile->year_of_birth = $secondary_profile['details']['year_of_birth'];
                    $additionalProfile->gender = $secondary_profile['details']['gender'];
                    $additionalProfile->firstname = '-';
                    $additionalProfile->lastname = '-';
                    $additionalProfile->user_groups = json_encode($secondary_profile['details']['selectedSensitivities']);
                    $additionalProfile->user_activities = json_encode($secondary_profile['details']['selectedActivities']);
                    $additionalProfile->save();

                }
                else {
                    $additionalProfile = AdditionalProfile::where('user_id',$user->id)->first();
                    if($additionalProfile)
                        $additionalProfile->delete();
                }

            }


            if (array_key_exists('place_id', $payload) && empty($payload['place_id']) == false) {
                $language = app('translator')->getFallback();
                $locationFields = $this->getLocationFields( $payload['place_id'], $language);
                $payload['city'] = $locationFields['city'];
                $payload['country'] = $locationFields['country'];
                $payload['location'] = $locationFields['location'];
                $payload['location_str'] = $locationFields['location_str'];
                $payload['place'] = $locationFields['place'];
            }

            
            $validator = app('validator')->make($payload, $user->rules());

            if ($validator->fails()) {
                return Responder::ValidationError( $validator->errors() );
            } else {
                // Process action and assign points/achievements to user
                // $actionUser = Gamification::processAction('UpdateProfile', $request->all());

                // add this profile update as a social activity record
                $updateProfileActivity = SocialActivity::firstOrCreate(['name' => 'UpdateProfile']);
                Event::fire(new SocialActivityAdded($updateProfileActivity->id, Auth::user()));

                if (isset($payload['password'])) {
                    $payload['password'] = Hash::make($payload['password']);
                }
                $user = User::find($id);

                if(!$request->has('gender'))
                {
                    unset($payload['gender']);
                }

                if(!$request->has('year_of_birth'))
                {
                    unset($payload['year_of_birth']);
                }


                //die('a)');
                $user->update($payload);


                $currentGroupsCount = $user->groups()->count();


                if (empty($groups) == false) {

                    if($currentGroupsCount == 0)
                        Event::fire(new Gamification($user,'UpdateProfile'));

                    $group_ids = [];
                    foreach($groups as $row) {
                        $group_ids[] = $row['id'];
                    }
                    $user->groups()->sync($group_ids);

                }
                else {


                    if($currentGroupsCount > 0) {
                        Event::fire(new Gamification($user,'RemoveProfile'));
                    }

                    $CurrentGroups = $user->groups()->get();

                    foreach($CurrentGroups as $cGroup) {
                        $user->groups()->detach($cGroup->id);
                    }

                }

                if (empty($outdoor_activities) == false) {
                    $outdoor_activity_ids = [];
                    foreach($outdoor_activities as $row) {
                        $outdoor_activity_ids[] = $row['id'];
                    }
                    $user->outdoorActivities()->sync($outdoor_activity_ids);
                }
                else {

                    $currentActivities = $user->outdoorActivities()->get();
                    foreach($currentActivities as $cActivity) {
                        $user->outdoorActivities()->detach($cActivity->id);
                    }

                }

                $user = $this->prepareRow($user);

                //$user['actionUser'] = $actionUser;

                return Responder::SuccessResponse( $user );
            }

        }
        catch (\Exception $e) {

            return Responder::ClientInputError($e->getMessage());

        }


    }

    /**
     * Update the specified user's display picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function UpdateProfilePicture(Request $request, $id)
    {
        if (Auth::id() != $id) {
            return Responder::UnauthorizedError();
        }

        $user = User::find($id);

        if (empty($user)) {
            return Responder::NotFoundError( $user );
        }

        $payload = $request->all();

        $rule = [
            'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
        $validator = app('validator')->make($payload, $rule);

        if ($validator->fails()) {
            return Responder::ValidationError( $validator->errors() );
        } else {
            // store thumbnail
            try {
                $file_path = 'public/uploads/profile/thumbs';
                $img = createImageThumb($request->file('profile_picture'), 300, 300, $file_path);
                $thumb_file_path = $img->dirname . '/' . $img->basename;
                $payload['profile_picture'] = URL::to(str_replace('public/', '', $thumb_file_path));
            } catch (Exception $e) {
                return Responder::ServerError('Could not save image.');
            }

            // unlink old profile picture
            if (empty($user->profile_picture) == false) {
                $old_picture_path = 'public/' . str_replace(URL::to('/'), '', $user->profile_picture);
                @unlink($old_picture_path);
            }

            $user = User::find($id);
            $user->update($payload);

            // TODO: add gamification stuff here

            // add this profile picture change as a social activity record
            $updateProfilePictureActivity = SocialActivity::firstOrCreate(['name' => 'UpdateProfilePicture']);
            Event::fire(new SocialActivityAdded($updateProfilePictureActivity->id, Auth::user()));

            $user = $this->prepareRow($user);

            return Responder::SuccessResponse( $user );
        }
    }

    /**
     * Delete the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::id() != $id) {
            return Responder::UnauthorizedError();
        }

        $user = User::find($id);

        if ($user) {

            // delete all related social activity
            $user->socialActivities()->delete();

            // deactivate user
            $user->active = 0;
            $user->save();
            $user->delete();

            $user = $this->prepareRow($user);
            return Responder::SuccessResponse( $user );
        } else {
            return Responder::NotFoundError( $user );
        }
    }

    protected function prepareRow($user)
    {
       // if($user->groups()->count() > 0 ) {
            foreach($user->groups as &$row) {
                $row['name'] = Lang::get('profile.groups.' . $row['name']);
            }
       // }

       // if($user->outdoorActivities()->count() > 0) {
            foreach($user->outdoorActivities as &$row) {
                $row['name'] = Lang::get('profile.outdoor_activities.' . $row['name']);
            }
       // }


        foreach($user->achievements as &$row) {
            $row['name'] = $row['name'];
        }
        $user['level'] = $user['level'];
        // Get related rows counts
        $user['sensors_count'] = $user->sensors()->count();
        $user['photos_count'] = $user->photosCount();
        $user['feelings_count'] = $user->perceptions()->count();

        // return followers and following users
        $user['followers'] = $user->followersUsers();
        $user['following'] = $user->followingUsers();

        $user['communities'] = $user->getCommunities();
        //$communities = SocialCommunity::where('user_id',Auth::id())->whereNotNull('deleted_at')->get();
        //$user['communities']

        // personal social activity feed
        $user['social_feed'] = UserSocialActivity::getUsersSocialActivityFeed([$user->id]);

        $additionalProfile = AdditionalProfile::where('user_id',$user->id)->first();

        if($additionalProfile) {

            $t = new \stdClass();
            $t->gender = $additionalProfile->gender;
            $t->year_of_birth = $additionalProfile->year_of_birth;
            $t->firstname = $additionalProfile->firstname;
            $t->lastname = $additionalProfile->lastname;
            $t->selectedSensitivities = json_decode($additionalProfile->user_groups);
            $t->selectedActivities = json_decode($additionalProfile->user_activities);

            $adProfile['details'] = $t;
            $adProfile['show'] = true;
            $user['secondary_profile'] = $adProfile;

        }

        return $user;
    }

    protected function getLocationFields($place_id, $language)
    {
        $results = [];

        try {
            $place_details = \GoogleMaps::load('placedetails')
                ->setParam ([
                    'placeid'   => $place_id,
                    'language' => $language
                ])
                ->get();
            $place_details_obj = json_decode($place_details);
            $place_details_obj = $place_details_obj->result;
            $latLng = $place_details_obj->geometry->location;
            $results['place'] = $place_details_obj;
            $results['location'] = [
                "type" => "Point",
                "coordinates" => [
                    $latLng->lng,
                    $latLng->lat
                ]
            ];
            $parserResults = GeoResultParser::parse($place_details_obj, ['locality', 'country']);
            $results['city'] = $parserResults['locality']->long_name;
            $results['country'] = $parserResults['country']->long_name;
            $results['location_str'] =  $results['city'] . ', ' . $parserResults['country']->short_name;

        } catch (Exception $e) {
            Log::info("Could not query Google Places API.\n");
        }

        return $results;
    }

    public function sendResetPasswordEmail(Request $request)
    {
        $validator = \Validator::make($request->all(), ['email' => 'required|email|min:4']);

        if($validator->fails()) {
            return Responder::ValidationError($validator->errors());
        }

        $user = User::where('email','=',$request->input('email'))->first();

        if(isset($user->id) == true) {
            $userActivation = new UserActivation();
            $userActivation->user_id  = $user->id;
            $userActivation->token = UserActivation::generateToken();
            $userActivation->save();

            Event::fire(new PasswordResetRequested($user, $userActivation->token));
            return Responder::SuccessResponse(Lang::get('responses.change_password_email'));
        }
        else {
            return Responder::NotFoundError(Lang::get('responses.email_not_exists'));
        }
    }

    public function showPasswordResetView($reset_password_code)
    {

        $userActivation = UserActivation::where('token',$reset_password_code)->first();

        if(!$userActivation || empty($reset_password_code)) {
            return redirect(env('WEB_URL'));
        }

        return view('misc.passChange')
            ->with('reset_password_code',$reset_password_code)
            ->with('action_url','/users/password/change');
    }

    public function showResetPassSuccess($success)
    {

        return view('misc.successPassChange')
            ->with('success',$success);
    }

    /**
     * Resets a user's password using a reset token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'password' => 'required|min:4',
            'confirm' => 'required|same:password',
            'token'=>'required'
        ]);


        if($validator->fails() || empty($request->input('token')))
            return redirect(env('API_URL').'/reset-password/success/0');
        else {

            $userActivation = UserActivation::where('token',$request->input('token'))->first();

            if ($userActivation) {
                $user = User::findOrFail($userActivation->user_id);
                $user->password = Hash::make($request->input('password'));
                $user->save();
                $userActivation->delete();

                return redirect(env('API_URL').'/reset-password/success/1');
                //return Responder::SuccessResponse(Lang::get('responses.changed_password'));

            } else {
                die();
            }

        }
    }

    /**
     * Update an authenticated user's password
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|min:4',
            'confirm' => 'required|same:password',
        ]);

        if ($validator->fails())
            return Responder::ValidationError($validator->errors());
        else {
            $user = Auth::user();
            // check input password against database hash
            $pswCheck = Hash::check($request->input('old_password'), $user->password);
            if ($pswCheck) {
                $user->password = Hash::make($request->input('password'));
                $user->save();
                return Responder::SuccessResponse(Lang::get('responses.updated_password'));
            } else {
                return Responder::ValidationError(Lang::get('responses.wrong_password'));
            }
        }
    }

    public function unsubscribe($unsubscribe_token)
    {
        if (!$unsubscribe_token) {
            return redirect(env('WEB_URL').'/unsubscribe/' . $unsubscribe_token . '?status=false');
        }

        $user = User::where('unsubscribe_token',$unsubscribe_token)->first();

        if (!$user) {
            return redirect(env('WEB_URL').'/unsubscribe/' . $unsubscribe_token . '?status=false');
        }

        $user->notify_email = false;
        $user->save();
        return redirect(env('WEB_URL').'/unsubscribe/' . $unsubscribe_token . '?status=true');
    }

    public function stopEmails()
    {
        $user = User::findorfail(Auth::id());
        $user->notify_email = ($user->notify_email == true) ? false : true;
        $user->save();
        return Responder::SuccessResponse($user->notify_email);
    }

    public function accept_newsletters()
    {
        $user = User::findorfail(Auth::id());
        $user->accept_newsletters = ($user->accept_newsletters == true) ? false : true;
        $user->save();
        return Responder::SuccessResponse($user->accept_newsletters);
    }



    public function togglePrivate($id){
        if (Auth::id() != $id) {
            return Responder::UnauthorizedError();
        }

        $user = User::find($id);

        $isPrivate = $user->private;
        $user->private = !$isPrivate;

        $user->save();

        return Responder::SuccessResponse(Lang::get('responses.changed_private'));
    }

    /**
     * Creates or restores a followship
     * @param $userId, the user to be followed
     * @return \Illuminate\Http\JsonResponse
     */
    public function follow($userId)
    {
        $user = User::find($userId);
        if ($user) {

            // if there is no active followship, create/restore one
            if (!$user->hasFollower(Auth::id())) {

                $user->addFollower(Auth::id());
                // send an email to the user being followed
                Event::fire(new FollowerAdded($userId, Auth::id()));

                return Responder::SuccessResponse(Lang::get('responses.follower_added'));
            } else {
                return Responder::SuccessResponse(Lang::get('responses.follower_existed'));
            }

        } else {
            return Responder::NotFoundError(Lang::get('responses.user_not_found'));
        }
    }

    /**
     * Deletes an existing followship
     * @param $userId, the user to be unfollowed
     * @return \Illuminate\Http\JsonResponse
     */
    public function unfollow($userId)
    {
        $user = User::find($userId);
        if ($user) {
            // check if the request was from an active follower

            if ($user->hasFollower(Auth::id())) {

                // soft-delete followship
                $user->deleteFollower(Auth::id());
                return Responder::SuccessResponse(Lang::get('responses.follower_deleted'));
            } else {
                return Responder::NotFoundError(Lang::get('responses.follower_not_found'));
            }
        } else {
            return Responder::NotFoundError(Lang::get('responses.user_not_found'));
        }
    }

    /**
     * Creates a new follower invite and triggers the FollowerInvited event
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function inviteFollower(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails())
            return Responder::ValidationError($validator->errors());
        else {
            // check if email belongs to an already registered user
            $user = User::withTrashed()->where('email', $request->input('email'))->first();
            if (!$user) {
                // trigger the event which will send the email
                Event::fire(new FollowerInvited(Auth::user(), $request->input('email')));

                return Responder::SuccessResponse(Lang::get('responses.follower_invited'));
            } else {
                return Responder::ClientInputError(['error' => Lang::get('responses.user_already_exists'), 'user' => $user]);
            }

        }

    }

    /**
     * Toggles user's social activity setting
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleAllSocialActivitySwitch()
    {
        $user = User::find(Auth::id());

        // toggle social activity setting
        $isHidden = $user->activities_visible;
        $user->activities_visible = !$isHidden;
        $user->save();

        // return appropriate message
        $message = $user->activities_visible ?
            Lang::get('responses.social_activity_enabled') : Lang::get('responses.social_activity_disabled');

        return Responder::SuccessResponse($message);

    }

    /**
     * Toggles the visibility for a specific user social activity
     * @param $userActivityId
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleSocialActivitySwitch($userActivityId)
    {
        // fetch user activity
        $userActivity = UserSocialActivity::where('id', $userActivityId)
            ->where('user_id', Auth::id())
            ->first();

        // if exists, toggle its visible attribute
        if ($userActivity) {
            $userActivity->visible = !$userActivity->visible;
            $userActivity->save();

            $message = $userActivity->visible ?
                Lang::get('social.user_social_activity_visible') : Lang::get('social.user_social_activity_hidden');

            return Responder::SuccessResponse($message);

        } else {
            return Responder::NotFoundError();
        }

    }

    /**
     * Display general social feed from following users (and personal)
     * @return Response
     */
    public function socialFeed()
    {
        $user = Auth::user();

        // fetch following users ids
        $followingUserIds = $user->followingUsers()->pluck('id');

        // add users id (feed will also include personal activities)
        // well, it seems it wont... commenting out this
//        $followingUserIds->push($user->id);

        // fetch social activities
        $activities = UserSocialActivity::getUsersSocialActivityFeed($followingUserIds,20);

        return Responder::SuccessResponse($activities);

    }

    /**
     * Returns all the social communities the user is a member of
     * @return \Illuminate\Http\JsonResponse
     */
    public function socialCommunities()
    {
        $communities = Auth::user()->socialCommunities();

        if (count($communities)) {
            return Responder::SuccessResponse($communities);
        } else {
            return Responder::NotFoundError(Lang::get('responds.no_member_of_communities'));
        }
    }

    /**
     * Returns all the social communities the user is an owner
     * @return \Illuminate\Http\JsonResponse
     */
    public function mySocialCommunities()
    {
        $communities = Auth::user()->mySocialCommunities();

        if (count($communities)) {
            return Responder::SuccessResponse($communities);
        } else {
            return Responder::NotFoundError(Lang::get('responds.no_owner_of_communities'));
        }
    }

    /**
     * Join a social community
     * @param $socialCommunityId
     * @return \Illuminate\Http\JsonResponse
     */
    public function joinSocialCommunity($socialCommunityId)
    {
        $community = SocialCommunity::find($socialCommunityId);

        if ($community) {
            $community->members()->attach(Auth::id());
            return Responder::SuccessResponse(Lang::get('responses.successfully_joined_community'));
        } else {
            return Responder::NotFoundError();
        }

    }

    public function getAchievements($userId) {

        $user = User::find($userId);
        $achievements = array();

        if($user->private) {
            return Responder::NotFoundError();
        }
        else {
            $achievements['acquired'] = $user->getAchievements();

            if(Auth::id() == $userId) {
                $achievementsDB = Achievement::All();
                $achievements['available'] = array();

                foreach($achievementsDB as $ach) {
                    $filename = explode('.',$ach->display_picture);
                    $ach->display_picture = $filename[0].'_Grayscale.'.$filename[1];
                    array_push( $achievements['available'], $ach);
                }

            }

            return Responder::SuccessResponse($achievements);

        }

    }

    /**
     * Join a social community
     * @param $socialCommunityId
     * @return \Illuminate\Http\JsonResponse
     */
    public function leaveSocialCommunity($socialCommunityId)
    {
        $community = SocialCommunity::find($socialCommunityId);

        if ($community) {
            // check if owner and restrict leave
            if ($community->owner->id == Auth::id()) {
                return Responder::ConflictError(Lang::get('responses.owner_try_to_leave'));
            } else {
                $community->members()->detach(Auth::id());
                return Responder::SuccessResponse(Lang::get('responses.successfully_left_community'));
            }
        } else {
            return Responder::NotFoundError();
        }

    }

    public function searchUser(Request $request) {

        $qTerm = $request->input('q');
        $results = array();

        $users = User::where(function($query) use ($qTerm)
        {
            $query->where('username', 'like', '%' .$qTerm . '%')
                ->orWhere('surname', 'like', '%' .$qTerm . '%')
                ->orWhere('name', 'like', '%' .$qTerm. '%');
        })
            ->whereNotNull('username')
            ->where('private','FALSE')
            ->get(['id','name','surname','username']);

        foreach($users as $user) {
            $u['id'] = $user->id;
            $u['name'] = $user->name;
            $u['surname'] = $user->surname;
            $u['username'] = $user->username;
            $u['type'] = 'Member';
            array_push($results,$u);

        }

        $communities = SocialCommunity::Where('name', 'like', '%' .$qTerm . '%')->get();

        foreach($communities as $com) {
            $c['id'] = $com->id;
            $c['name'] = $com->name;
            $c['type'] = 'Community';
            array_push($results,$c);
        }

        return Responder::SuccessResponse($results);

    }

    /**
     * Fetches user personalized recommendations from CERTH API
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecommendations(Request $request)
    {
        $lang = 'en';

        if($request->has('lang')) {
            $lang = $request->input('lang');
            if ($lang == 'nb') {
                $lang = 'no';
            }
        }

        $user = User::find(Auth::id());
        $currentDate = date('Y-m-d');

        $results = Recommendations::get($user,$currentDate,$currentDate,$lang,$request->input('city'),$request->input('lat'),$request->input('lon'));

        // handle defective cases
        if (is_null($results)) {
            return Responder::ServerError(Lang::get('responses.recommendation_api_error'));
        }

        if (empty($results)) {
            return Responder::ValidationError(Lang::get('responses.recommendation_api_invalid'));
        }

        return Responder::SuccessResponse($results);
    }

}


