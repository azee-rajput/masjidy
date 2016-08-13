<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Http\Requests;
use App\Like;
use App\Feed;
use App\Notification;
use App\Token;
class LikeController extends Controller
{
    public function likeUnlike(Request $request){
        $parameters = $request->only('feedId');
        $feedId = $parameters['feedId'];
        $id = $this->getLoggedInUser()->id;
        
        $isLike = Like::where('feed_id',$feedId)->where('user_id',$id)->count();
        $countedLikes = Feed::where('id',$feedId)->get(['like_count']);
        
        if ($isLike > 0){
            Like::where('feed_id',$feedId)->where('user_id',$id)->delete();
            $likeCount = $countedLikes[0]['like_count']-1;
            Feed::where('id',$feedId)->update(['like_count'=>$likeCount]);
            $status=true;
            $message='unlike successful';
            $isLiked = false;
            return response()->json(['status'=>$status, 'msg'=>$message,'isLiked'=>$isLiked]);
        }else{
            $like = new Like;
            $like->user_id = $id;
            $like->feed_id = $feedId; // optional
            $like->save();
            $likeCount = $countedLikes[0]['like_count']+1;
            Feed::where('id',$feedId)->update(['like_count'=>$likeCount]);
            $feed = Feed::where('id',$feedId)->first();
            $status=true;
            $message='like successful';
            $isLiked = true;
           
            if($feed->user_id != $id){
                $notification = new Notification;
                $notification->type = $notification->types['like'];

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
            
          
            
            
           
            
            
            return response()->json(['status'=>$status, 'msg'=>$message, 'isLiked'=>$isLiked]);
        }
        return 'Liked not working';
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
