<?php

namespace App\Http\Controllers\Api\v1;

use App\ForumReply;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Responder;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class ForumReplyController extends Controller
{

    public function __construct()
    {
        // apply authorization middleware
        $this->middleware('auth:api', ['except' => [
            'index',
            'show',
        ]]);
    }

    /**
     * Display resource index.
     * @return Response
     */
    public function index()
    {
        $replies = ForumReply::all();

        return Responder::SuccessResponse($replies);
    }

    /**
     * Display the specified resource.
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $reply = ForumReply::find($id);

        if ($reply) {
            return Responder::SuccessResponse($reply);
        } else {
            return Responder::NotFoundError();
        }
    }

    /**
     * Store a new resource.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $payload = $request->all();
        // add current user's id to payload
        $payload['author_id'] = Auth::id();
        $validator = app('validator')->make($payload, ForumReply::$rules);

        if ($validator->fails()) {
            return Responder::ValidationError($validator->errors());
        } else {
            $reply = ForumReply::create($payload);

            return Responder::SuccessCreateResponse($reply);
        }

    }

    /**
     * Update the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $reply = ForumReply::find($id);

        if ($reply) {
            // TODO: grant admin edit access, when ACL is implemented
            // you can only edit your own replies
            if ($reply->author->id == Auth::id()) {
                $payload = $request->all();

                // if the author_id isn't posted, add current user's id to payload
                if (!$request->has('author_id')) {
                    $payload['author_id'] = Auth::id();
                }

                $validator = app('validator')->make($payload, ForumReply::$rules);

                if ($validator->fails()) {
                    return Responder::ValidationError($validator->errors());
                } else {
                    $reply->update($payload);

                    // hide author data from the JSON response
                    $reply = $reply->makeHidden('author');

                    return Responder::SuccessResponse($reply);
                }
            } else {
                return Responder::UnauthorizedError();
            }
        } else {
            return Responder::NotFoundError();
        }
    }

    /**
     * Remove the specified resource (soft-delete)
     * @param  int $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $reply = ForumReply::find($id);

        if ($reply) {
            $reply->delete();

            return Responder::SuccessResponse($reply);
        } else {
            return Responder::NotFoundError();
        }

    }

}
