<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\SocialCommunity;
use App\UserSocialActivity;
use Illuminate\Http\Request;
use App\Libraries\Responder;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Lang;

class SocialCommunityController extends Controller
{

    public function __construct()
    {
        //
    }


    public function show($communityId)
    {
        $community = SocialCommunity::find($communityId);

        if($community)
            return Responder::SuccessResponse($community);
        else
            return Responder::NotFoundError();
    }


    public function getMembers($communityId) {

        $community = SocialCommunity::find($communityId);

        if($community) {
            $members = $community->getMembers();
            return Responder::SuccessResponse($members);
        }
        else {
            return Responder::NotFoundError();
        }
    }

    /**
     * Display social communities resource index.
     * Search based on search request parameter
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $term = trim($request->input('search'));

        if (empty($term)) {
            $communities = SocialCommunity::with('owner')->get();
        } else {
            // search based on community name or description
            $communities = SocialCommunity::with('owner')
                ->where(function ($query) use ($term) {
                    $query->where('social_communities.name', $term)
                        ->orWhere('social_communities.description', 'like', '%' . $term . '%');
                })
                ->get();
        }

        // TODO: hide unused data from the response
        return Responder::SuccessResponse($communities);
    }

    /**
     * Store a new social community.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $payload = $request->all();

        // create community data array
        // add current user's id
        $data = [
            'name' => $payload['name'],
            'description' => $payload['description'],
            'owner_id' => Auth::id()
        ];

        $validator = app('validator')->make($data, SocialCommunity::$rules);

        if ($validator->fails()) {
            return Responder::ValidationError($validator->errors());
        } else {
            $community = SocialCommunity::create($data);
            // also add the owner as a member
            $community->members()->attach(Auth::id());

            return Responder::SuccessCreateResponse($community,Lang::get('social.Created_Community'));
        }

    }

    /**
     * Update a social community.
     *
     * @param  Request $request
     * @param  int $socialCommunityId
     * @return Response
     */
    public function update(Request $request, $socialCommunityId)
    {
        $community = SocialCommunity::find($socialCommunityId);

        if ($community) {
            // TODO: grant admin edit access, when ACL is implemented
            // you can only edit your own communities
            if ($community->owner->id == Auth::id()) {
                $payload = $request->all();

                // if the owner_id isn't posted, add current user's id to payload
                if (!$request->has('owner_id')) {
                    $payload['owner_id'] = Auth::id();
                }

                $validator = app('validator')->make($payload, SocialCommunity::$rules);

                if ($validator->fails()) {
                    return Responder::ValidationError($validator->errors());
                } else {
                    $community->update($payload);

                    // hide author data from the JSON response
                    $community = $community->makeHidden('owner');

                    return Responder::SuccessResponse($community,Lang::get('social.Updated_Community'));
                }
            } else {
                return Responder::NotFoundError();
            }
        } else {
            return Responder::NotFoundError();
        }
    }

    /**
     * Remove a social community (soft-delete)
     *
     * @param  int $socialCommunityId
     * @return Response
     */
    public function destroy($socialCommunityId)
    {
        $community = SocialCommunity::find($socialCommunityId);

        if ($community) {
            // TODO: grant admin edit access, when ACL is implemented
            // you can only delete social communities you own
            if ($community->owner->id == Auth::id()) {

                // delete user memberships (this is not soft-detele)
                $community->members()->detach();

                $community->delete();

                return Responder::SuccessResponse($community,Lang::get('social.Deleted_Community'));
            } else {
                return Responder::UnauthorizedError();
            }
        } else {
            return Responder::NotFoundError();
        }

    }


    /**
     * Returns the social community feed
     *
     * @param  int $socialCommunityId
     * @return Response
     */
    public function feed($socialCommunityId)
    {
        $community = SocialCommunity::find($socialCommunityId);

        if ($community) {

            $communityActivities = array();
            $memberIds = $community->members()->pluck('users.id');
            $community->num_of_members = count($memberIds);
            $community->joined_member = false;

            if($community->isMember(Auth::id())) {
                $communityActivities = UserSocialActivity::getUsersSocialActivityFeed($memberIds, 20);
                $community->joined_member = true;

            }

            $response['community'] = $community;
            $response['feeds'] = $communityActivities;

            return Responder::SuccessResponse($response);
        } else {

            return Responder::NotFoundError();
        }

    }
}
