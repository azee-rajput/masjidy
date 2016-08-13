<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Http\Requests;

use App\Comment;
use App\User;
use App\Feed;

use App\Notification;
use App\Token;
class CommentController extends Controller
{
    
    public function showComment($feedId){
        $check = Feed::where('id',$feedId)->count();
        if($check > 0){
            $comments = Comment::where('feed_id',$feedId)->join('users','comments.user_id','=','users.id')
                ->orderBy('comments.id','desc')->get(["comments.id", "feed_id", "user_id", "text", "is_deleted", "email", "name", "mobile", 'comments.created_at', 'profile_picture']);
            $status = true;
            $msg = "comments retrieved successfully";
            return response()->json(['status'=>$status,'msg'=>$msg,'comments'=>$comments]);
        }
        $status = false;
        $msg = "something went wrong";
        return response()->json(['status'=>$status,'msg'=>$msg]);
    }
    
    public function createComment(Request $request){
        $parameters = $request->only('feedId','comment');
        $feedId = $parameters['feedId'];
        $commText = $parameters['comment'];
        $id = $this->getLoggedInUser()->id;
        
        $countedComments = Feed::where('id',$feedId)->get(['comment_count']);
        $feed = Feed::where('id',$feedId)->first();
        $comment = new Comment;
        $comment->feed_id = $feedId;
        $comment->text = $commText;
        $comment->user_id = $id;
        $comment->save();
        
        
        if($feed->user_id != $id){
            $notification = new Notification;
            $notification->type = $notification->types['comment'];

            $notification->user_id = $feed->user_id;
            $notification->relation_user_id = $id;
            $notification->feed_id = $feed->id;
            $notification->save();
            
            $tokens = Token::where('user_id', $notification->user_id)->get(['device_token']);
            
                $deviceTokens = array();
                foreach($tokens as $token){
                        array_push($deviceTokens, $token->device_token);
                }


                $notification->sendNotification("Test Message Hello",$deviceTokens);
            
           
        }
            
        
        $commentCount = $countedComments[0]['comment_count']+1;
        Feed::where('id',$feedId)->update(['comment_count'=>$commentCount]);
        
        $status=true;
        $message='comment successful';
        return response()->json(['status'=>$status, 'msg'=>$message, 'created_comment'=>$comment]);
        
    }
    
    public function deleteComment(Request $request){
        $parameters = $request->only('commId','feedId');
        $commId = $parameters['commId'];
        $feedId = $parameters['feedId'];
        $id = $this->getLoggedInUser()->id;
        
        $countedComments = Feed::where('id',$feedId)->get(['comment_count']);
        $check = Comment::where(['id'=>$commId,'user_id'=>$id])->count();
        
        if ($check > 0){
            Comment::where(['id'=>$commId,'user_id'=>$id])->delete();
            $commentCount = $countedComments[0]['comment_count']-1;
            Feed::where('id',$feedId)->update(['comment_count'=>$commentCount]);
            $status=true;
            $message='comment delete successful';
            return response()->json(['status'=>$status, 'msg'=>$message]);
        }
        
        $status=false;
        $message='comment cannot be deleted';
        return response()->json(['status'=>$status, 'msg'=>$message]);
        
    }
    
    
    
    public function getLoggedInUser(){
        try{
            $user=JWTAuth::parseToken()->toUser();
            
            if(! $user){
                return null;
//                return $this->response->errorNotFound("User Not Found");
            }
        }
        catch (\Tymon\JWTAuth\Exceptions\JWTException $ex){
                return null;
            //          return $this->response->error('Something went wrong');
        }
        
        return $user;
    }
}
