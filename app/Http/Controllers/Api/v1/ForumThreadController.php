<?php

namespace App\Http\Controllers\Api\v1;

use App\ForumThread;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Responder;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class ForumThreadController extends Controller
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
        $threads = ForumThread::with('author')
            ->with('tags')
            ->get();

        // TODO: hide unused data from the response

        return Responder::SuccessResponse($threads);
    }

    /**
     * Display the specified resource.
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $thread = ForumThread::where('id',$id)
            ->with('author')
            ->with('replies')
            ->get();

        if ($thread) {
            return Responder::SuccessResponse($thread);
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
        $validator = app('validator')->make($payload, ForumThread::$rules);

        if ($validator->fails()) {
            return Responder::ValidationError($validator->errors());
        } else {
            $reply = ForumThread::create($payload);

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
        $thread = ForumThread::find($id);

        if ($thread) {
            // TODO: grant admin edit access, when ACL is implemented
            // you can only edit your own threads
            if ($thread->author->id == Auth::id()) {
                $payload = $request->all();

                // if the author_id isn't posted, add current user's id to payload
                if (!$request->has('author_id')) {
                    $payload['author_id'] = Auth::id();
                }

                $validator = app('validator')->make($payload, ForumThread::$rules);

                if ($validator->fails()) {
                    return Responder::ValidationError($validator->errors());
                } else {
                    $thread->update($payload);

                    // hide author data from the JSON response
                    $thread = $thread->makeHidden('author');

                    return Responder::SuccessResponse($thread);
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
        $thread = ForumThread::find($id);

        if ($thread) {
            $thread->delete();

            return Responder::SuccessResponse($thread);
        } else {
            return Responder::NotFoundError();
        }

    }

}
