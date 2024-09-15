<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Feed;
use App\Models\Like;
use App\Models\Comment;

class FeedController extends Controller
{
    public function store(Request $request){
        $validate = Validator::make($request->all(), 
        [
            'content' => 'required|string|min:6 ', //10mb
        ]);
    
        if($validate->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validate->errors()
            ], 401);
        }

        $new_feed  = new Feed();
        $new_feed->content = $request->content;
        $new_feed->user_id = auth()->user()->id;
        $new_feed->save();
        

        return response()->json([
            'status' => true,
            'message' => 'feed created successfully',
            'feed' => $new_feed,
        ], 200);
    }

    public function likePost($id){
        $feed = Feed::find($id);
        $like = Like::where('feed_id', $feed->id)->where('user_id', $feed->user_id)->first();

        if($feed and !$like){
            $like = new Like();
            $like->user_id = auth()->user()->id;
            $like->feed_id = $id;
            $like->save();

            return response()->json([
                'status' => true,
               'message' => 'liked  post successfully',
            ], 200);
        }
        else if($feed and $like){
            $like->delete();

            return response()->json([
                'status' => true,
               'message' => 'unliked  post successfully',
            ], 200);
        }
        else{
            return response()->json([
                'status' => false,
               'message' => 'Feed not found',
            ], 404);
        }
    }

    public function deletePost($id){
        $feed = Feed::find($id);

        if($feed){
            if($feed->user_id == auth()->user()->id){
                $feed->delete();
            }
            else{
                return response()->json([
                    'status' => false,
                   'message' => 'You are not authorized to delete this feed',
                ], 403);
            }
        }
        else{
            return response()->json([
                'status' => false,
               'message' => 'Feed not found',
            ], 404);
        }
    }

    public function showFeed($id){
        $feed = Feed::with('user', 'likes', 'comments')->where('id', $id)->first();

        if($feed){
            return response()->json([
                'status' => true,
               'message' => 'Feed found successfully',
                'feed' => $feed,
            ], 200);
        }
        else{
            return response()->json([
                'status' => false,
               'message' => 'Feed not found',
            ], 404);
        }
    }

    public function showAllFeeds(){
        $feeds = Feed::with('user', 'likes', 'comments')->get();

        return response()->json([
            'status' => true,
           'message' => 'Feeds found successfully',
            'feeds' => $feeds,
        ], 200);
    }

    public function showUserFeeds($userId){
        $feeds = Feed::where('user_id', $userId)->with('user', 'likes', 'comments')->get();

        return response()->json([
            'status' => true,
           'message' => "User's Feed found successfully",
            'feeds' => $feeds,
        ], 200);
    }

    public function commentAction($id, Request $request){
        $feed = Feed::find($id);

        if($feed){
            $new_comment = new Comment();
            $new_comment->user_id = auth()->user()->id;
            $new_comment->feed_id = $id;
            $new_comment->content = $request->content;
            $new_comment->save();
        }

        return response()->json([
            'status' => true,
           'message' => 'Comment added successfully',
            'comment' => $new_comment,
        ], 200);

    }

    public function getComments($id){
        $comments = Comment::with('user')->where('feed_id', $id)->get();

        return response()->json([
            'status' => true,
           'message' => 'Comment retrieved successfully',
            'comment' => $comments,
        ], 200);
    }
}
