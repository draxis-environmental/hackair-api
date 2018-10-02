<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\ForumTag;
use App\Libraries\Responder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ForumTagController extends Controller
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
     *
     * @return Response
     */
    public function index()
    {
        $tags = ForumTag::all();

        return Responder::SuccessResponse($tags);
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $tag = ForumTag::where('id',$id)
            ->with('threads')
            ->get();

        if ($tag) {
            return Responder::SuccessResponse($tag);
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
        $validator = app('validator')->make($payload, ForumTag::$rules);

        if ($validator->fails()) {
            return Responder::ValidationError($validator->errors());
        } else {
            $tag = ForumTag::create($payload);

            return Responder::SuccessCreateResponse($tag);
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

        $tag = ForumTag::find($id);

        if ($tag) {
            $payload = $request->all();
            $validator = app('validator')->make($payload, ForumTag::$rules);

            if ($validator->fails()) {
                return Responder::ValidationError($validator->errors());
            } else {
                $tag->update($payload);

                return Responder::SuccessResponse($tag);
            }
        }
    }


    /**
     * Remove the specified resource (soft-delete)
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $tag = ForumTag::find($id);

        if ($tag) {
            $tag->delete();

            return Responder::SuccessResponse($tag);
        } else {
            return Responder::NotFoundError();
        }

    }

}
