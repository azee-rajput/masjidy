<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Feed;
use App\User;
use App\Follow;
use App\Like;
use App\Comment;
use App\Share;
use App\Notification;
use App\Token;

use DB;

use App\Http\Requests;

class FeedController extends Controller
{
    
////////////////////////creating a new feed///////////////////////
    public function createFeed(Request $request){
        $parameters = $request->only('feed','type','media','thumb', 'feed_id');
        $id=$this->getLoggedInUser()->id;
        $feed = new Feed;
        $types = array('url' => 1, 'image' => 2, 'video' => 3, 'text' => 4, 'shared' =>5);
        $type = $parameters['type'];
        
        
        if ($type == 'image'){
            $imageName = $id.'_'.md5(time().uniqid()).'.jpeg';
            $destinationPath = public_path() .'/images/'.$id.'/feed_images/';
            file_put_contents($destinationPath.$imageName, base64_decode($parameters['media']));
            $feed->url = '/images/'.$id.'/feed_images/'.$imageName;
          //  $file->move($destinationPath, $imageName);
        }else if($type == 'url'){
           
            $feed->url = $parameters['media'];
            
        }else if ($type == 'video'){
            $filename = $parameters['media'];
            $imageName = $id.'_'.md5(time().uniqid()).'.jpeg';
            $destinationPath = public_path() .'/images/'.$id.'/feed_thumbs/';
            file_put_contents($destinationPath.$imageName, base64_decode($parameters['thumb']));
            $feed->video_thumb = '/images/'.$id.'/feed_thumbs/'.$imageName;
//            $videoName = $id.'_'.md5(time().uniqid()).'.mp4';
//            $destinationPath = public_path() .'/images/'.$id.'/feed_videos/';
//            $input = $parameters['media']->move($destinationPath,$videoName);
            $source = public_path() .'/temp/'.$filename;
            $destination = public_path() .'/images/'.$id.'/feed_videos/'.$filename;
            
            
           rename($source,$destination);
            
            $feed->url = '/images/'.$id.'/feed_videos/'.$filename;
        }else if($type == 'shared'){
            $feed->shared_feed_id = $parameters['feed_id'];
        }
            
        
        
        $feed->type = $types[$type];
        $feed->text = $parameters['feed'];
        $feed->user_id = $id;
        
        $feed->save();
        $status=true;
        $message='feed created successfully';
        return response()->json(['status'=>$status,'msg'=>$message]);
  
    }
 
/////////////////////////////////uploading video on server in temporary folder///////////////////
    public function videoUpload(Request $request){
//         $parameters = $request->only('media');
//        $input = $parameters['media'];
//        $videoName = $input->getClientOriginalName();
//        $destinationPath = public_path() .'/images/';
//        $input = $parameters['media']->move($destinationPath,$videoName);
//        $status=true;
//        $message='video uploaded successfully';
//        return response()->json(['status'=>$status,'msg'=>$message]);
        
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $id = $this->getLoggedInUser()->id;
            $videoName = $id.'_'.md5(time().uniqid()).'.mp4';
            $file_name = $_FILES['myFile']['name'];
            $file_size = $_FILES['myFile']['size'];
            $file_type = $_FILES['myFile']['type'];
            $temp_name = $_FILES['myFile']['tmp_name'];
            if($temp_name == ""){
                $status=false;
                $message='video could not be uploaded';
                //return response()->json(['status'=>$status,'msg'=>$message]);
            }
            $location = public_path().'/temp/';
            
            

            move_uploaded_file($temp_name, $location.$videoName);
            

            $status=true;
            $message='video uploaded successfully';
            return response()->json(['status'=>$status,'msg'=>$message,'name'=>$videoName]);
            
            //echo public_path() .'/images/temp'.$videoName;
        }else{
            $status=false;
            $message='video could not be uploaded';
            return response()->json(['status'=>$status,'msg'=>$message]);
        }
        
    }
    
//////////////////showing current user feeds and feeds of his followings//////////////////////////////////
    public function showFeed($offset = 0){
        $greater;
        $id=$this->getLoggedInUser()->id;
        $followedId=Follow::where('follower_id',$id)->get(['followed_id']);
        $followedId['follower_id']=$id;
       
        
        //return $greater;
        //return $shareTable->created_at;
        $feeds = Feed::where('is_deleted',0)->join('users','feeds.user_id','=','users.id')->WhereIn('feeds.user_id',$followedId)->take(5)->offset($offset * 5)
            ->where(function ($query){
                $greater='feeds.created_at';
                $query->orderBy($greater,'desc');
            })->orderBy('feeds.created_at', 'desc')->get(['name','feeds.user_id','feeds.id','email','mobile','profile_picture','feeds.created_at','feeds.url','feeds.text','feeds.comment_count','feeds.like_count','is_anonymous','type','video_thumb', 'shared_feed_id']);
        /*
        join('share',feeds.id,'=',share.feed_id)->where(share.shared_by,$id)
        ->orderBy('feeds.created_at','desc')
        
        */
        
        $feedIds = array();
        
    //    $feedCollection = collect($feeds);
        //$i = 0;
       /* $feeds->each(function($feed){
            global $i, $feedIds;
            $feedIds[$i] = $feed->id;
            return $feed;
            $i++;
        });*/
        
        foreach($feeds as $feed){
            
            if($feed->type =="shared"){
                $feed->shared_feed = Feed::where('feeds.id', $feed->shared_feed_id)->join('users', 'users.id', '=', 'feeds.user_id')->get();
            }
            
            $likes = Like::where('feed_id', $feed->shared_feed_id)->where('user_id', $id)->count();
           
            if($likes>0){
                $feed->is_liked = true;
            }else{
                $feed->is_liked = false;
            }
            array_push($feedIds, $feed->id);
        }
        
        $likes = Like::whereIn('feed_id', $feedIds)->where('user_id',$id)->get(['feed_id']);
        $userName = User::where('id',$id)->get(['name']);
        
        //return
        
        $likedFeeds = array();
         
        foreach($likes as $item){
            array_push($likedFeeds, $item->feed_id);
        }
        
        
       
        foreach($feeds as $feed){
            if(in_array($feed->id, $likedFeeds)){
                $feed->isLiked = true;
             }else{
                 $feed->isLiked = false;
             }
        }
        
       
       // return $likes;
        
        
      
        
        //return $feedIds;
//        $feedId = $feeds;
//        $like = Like::whereIn('feed_id',$feedId)->where('user_id',$id)->get();
        return $this->response->array(compact('feeds'));
        
      // $feeds=DB::select('select * from feeds where user_id in (Select followed_id from follows where follower_id =:result) or user_id = :result2',['result'=>$id,'result2'=>$id]);
      //  return $feeds;
    }
    
    
    public function getFeedById($id){
        
        $userId = $this->getLoggedInUser()->id;
        $feed = Feed::where('feeds.id',$id)->join('users','feeds.user_id','=','users.id')->first(['name','feeds.user_id','feeds.is_deleted','feeds.id','email','mobile','profile_picture','feeds.created_at','feeds.url','feeds.text','feeds.comment_count','feeds.like_count','is_anonymous','type','video_thumb', 'shared_feed_id']);
        
        if($feed != null && $feed->is_deleted == '0'){
            $likes = null;
             if($feed->type =="shared"){
                $feed->shared_feed = Feed::where('feeds.id', $feed->shared_feed_id)->join('users', 'users.id', '=', 'feeds.user_id')->get(['feeds.id', 'feeds.text', 'feeds.created_at', 'feeds.user_id', 'feeds.type', 'feeds.url', 'feeds.video_thumb', 'feeds.comment_count', 'feeds.like_count', 'feeds.is_deleted', 'feeds.is_anonymous', 'users.email', 'users.name', 'users.profile_picture', 'users.dob']);
                  $likes = Like::where('feed_id', $feed->shared_feed_id)->where('user_id', $userId)->count();
             }else{
                   $likes = Like::where('feed_id', $feed->id)->where('user_id', $userId)->count();
             }
            
            
           
           
                if($likes>0){
                    $feed->is_liked = true;
                }else{
                    $feed->is_liked = false;
                }
            
            return response()->json(['status'=>true,'msg'=>'Feed found successfully', 'feed'=>$feed]);
        }
        
        return response()->json(['status'=>false,'msg'=>'Feed not found']);
    }
    
////////////////////deleting a feed/////////////////////////
    public function deleteFeed(Request $request){
        $params =$request->only('feed_id');
        $id=$this->getLoggedInUser()->id;
        $check = Feed::where('id',$params['feed_id'])->orWhere('shared_feed_id', $params['feed_id'])->get();
        $query = Feed::where('id',$params['feed_id'])->orWhere('shared_feed_id', $params['feed_id'])->update(['is_deleted'=>1]);
        
        $checking = $check[0]['is_deleted'];
        //return $checking;
        if($checking!=1){
            $status='true';
            $message='Feed deleted successfully';
            return response()->json(['status'=>$status, 'msg'=>$message]); 
        }else{
            $status=false;
            $message='Feed does not exists';
            return response()->json(['status'=>$status,'msg'=>$message]);
        }
        return 'hello';
        
    }
  
///////////////////////showing other users feeds///////////////////////////
    public function followFeed($offset = 0,$id){
        
        $feeds = Feed::where('is_deleted',0)->where('user_id',$id)->join('users','feeds.user_id','=','users.id')
            ->take(5)->offset($offset * 5)->orderBy('feeds.id','desc')->get(['name','user_id','feeds.id','email','mobile','profile_picture','feeds.created_at','feeds.url','feeds.text','feeds.comment_count','feeds.like_count','is_anonymous','type','video_thumb']);
        
        foreach($feeds as $feed){
            
            if($feed->type == "shared"){
                $feed->shared_feed = Feed::where('id', $feed->shared_feed_id)->get(); 
            }
            
        }
        
        $feedIds = array();
        
        foreach($feeds as $feed){
            array_push($feedIds, $feed->id);
        }
        
        $likes = Like::whereIn('feed_id', $feedIds)->where('user_id',$id)->get(['feed_id']);
        
        $likedFeeds = array();
        
        
        foreach($likes as $item){
            array_push($likedFeeds, $item->feed_id);
        }
        
        
       // return $likeList;
       
        foreach($feeds as $feed){
            if(in_array($feed->id, $likedFeeds)){
                $feed->isLiked = true;
             }else{
                 $feed->isLiked = false;
             }
        }
        
        return $this->response->array(compact('feeds'));
        
    }
    
///////////////////////showing specific feed,comments,likes//////////////////////
    public function specificFeed(Request $request){
        $feedId = $request->only('feedId');
        $id=$this->getLoggedInUser()->id;
        
        $likeById = Like::where('feed_id', $feedId)->get(['user_id']);
        $likeBy = User::whereIn('id',$likeById)->get(['name']);
        
        
//        $comment = Comment::where('feed_id',$feedId)->join('users','users.id','=','comments.user_id')->orderBy('id','desc')->get(['comments.text','','users.name']);
//        $commentText = $comment[0]['text'];
//        $commentById[] = $comment[0]['user_id'];
//        $commentBy = User::whereIn('id',$commentById)->get(['name']);
        
        $status=true;
        $message='displaying specific feed';
        return response()->json(['status'=>$status,'msg'=>$message, 'likedBy'=>$likeBy]);
    }
    
/////////////// sharing feed /////////////////////////////////
    public function share(Request $request){
        
        $id=$this->getLoggedInUser()->id;
        $parameters = $request->only('feedId');
        
        
        $feed = new Feed;
        $feed->type = 5;
        $feed->shared_feed_id = $parameters['feedId'];
        
        $shared_feed = Feed::where('id', $feed->shared_feed_id)->first();
        $feed->user_id = $id;
        
        
        
        //$feedShare = Feed::where('id',$feedId['feedId'])->get(['user_id']);
        
       // $share->shared_from = $feedShare[0]['user_id'];
        $feed->save();
        
        if($shared_feed->user_id != $id){
            $notification = new Notification;
            $notification->type = $notification->types['share'];

            $notification->user_id = $shared_feed->user_id;
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
        
        $status=true;
        $message='Post shared successfully';
        return response()->json(['status'=>$status,'msg'=>$message]);
    }
    
/////////////////token validating function////////////////////////////////
///////////this function is used in almost every other function//////////////////
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
