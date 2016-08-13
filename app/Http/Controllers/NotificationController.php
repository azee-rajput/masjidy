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

class NotificationController extends Controller
{
    
    public function getNotifications(){
        $userId = $this->getLoggedInUser()->id;  
        
        $notifications = Notification::where('user_id',$userId)->join('users','notifications.relation_user_id','=','users.id')->orderBy('notifications.created_at','desc')->get(['notifications.id', 'notifications.is_new', 'notifications.is_seen', 'notifications.created_at', 'notifications.feed_id', 'notifications.user_id', 'notifications.type', 'notifications.relation_user_id', 'users.name', 'users.email', 'users.profile_picture']);
        $status = true;
        $msg = "notifictions retrieved successfully";
        return response()->json(['status'=>$status,'msg'=>$msg,'notifications'=>$notifications]);
        
        
    }
    
    public function notificationsViewed(){
        
         $userId = $this->getLoggedInUser()->id;  
        Notification::where('user_id',$userId)->update(['is_new'=> 0]); 
        
        return response()->json(['status'=>true,'msg'=>'New Notifications Updated']);
    }
    
    public function notificationVisited(Request $request){
        $parameters = $request->only('notificationId');
        return Notification::where('id', $parameters['notificationId'])->update(['is_seen'=>1]);
        return response()->json(['status'=>true,'msg'=>'Notification Visited Successfully']);
    }
    
    public function getNewCount(){
        $userId = $this->getLoggedInUser()->id;     
        $newNotifications = Notification::where('user_id',$userId)->where('is_new', 1)->count(); 
        
        return response()->json(['status'=>true,'msg'=>'New Notifications Count', 'count'=>$newNotifications]);
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
