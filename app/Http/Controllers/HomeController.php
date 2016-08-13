<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Follow;

use App\Role;
use App\Token;
use App\Notification;
use App\Permission;

use File;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class HomeController extends Controller
{
    public function index(){
        return User::all();
    }
    
    public function attachUserRole($userId, $role){
        $user =User::find($userId);
        $roleid=Role::where('name', $role)->first();
        $user->roles()->attach($roleid);
        
        return $user;
    }
    
    public function getUserRole($userid){
        return User::find($userid)->roles;
    }
    
    public function testNotification(){
        // API access key from Google API's Console
        
        $registrationIds = array('dNfPrtfNpnE:APA91bGUa04Yb1Rd1nO8L5a3s_5eVepeqWG19v5BdSAuRN8eFo1mFS5TOZFSgsUlWVzy2vD2qJ-ci7_2J7w4GkfUeoORVp0SHP5oN6w3Kkq_q-uR3jA6o3mnBjWhvcAYHUJ-gt5llDrb');
        // prep the bundle
        $msg = array
        (
//            'message' 	=> 'here is a message. message',
//            'title'		=> 'This is a title. title',
//            'subtitle'	=> 'This is a subtitle. subtitle',
//            'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
//            'vibrate'	=> 1,
//            'sound'		=> 1,
//            'largeIcon'	=> 'large_icon',
//            'smallIcon'	=> 'small_icon'
            
            "score"=>"3x1"
        );
        
        $notification = array
        (
            'body' 	=> 'here is a message. body',
            'title'		=> 'This is a title. title',
            'sound' => 'default'
        );
        $fields = array
        (
            //'registration_ids' 	=> $registrationIds,
            'to' => 'dNfPrtfNpnE:APA91bGUa04Yb1Rd1nO8L5a3s_5eVepeqWG19v5BdSAuRN8eFo1mFS5TOZFSgsUlWVzy2vD2qJ-ci7_2J7w4GkfUeoORVp0SHP5oN6w3Kkq_q-uR3jA6o3mnBjWhvcAYHUJ-gt5llDrb',
            'data'			=> $msg,
             'notification'	=> $notification
            
        );

        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        echo $result;   
    }
    
/////////////////////////////////google get mosques map apis/////////////////////////////////////////
    public function getMapMosque($lat,$lng){
        $url = "https://maps.googleapis.com/maps/api/place/search/json?location=$lat,$lng&rankby=distance&sensor=true&key=AIzaSyDI2XjZcpklM6z4dB2XrRmh0GLMDVmnXe0&types=mosque";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response,true);

        $arrStatus = $response_a['status'];
//            return response()->json([$arrStatus]);
        if($arrStatus == "ZERO_RESULTS"){
            $status = false;
            $msg = 'no results found';
            return response()->json(['msg'=>$msg, 'status'=>$status,"no. of results"=>0, "array"=>$arrStatus]);
        }else{
            $resultCount = 0;
            foreach($response_a['results'] as $result){
                $resultCount++;
                $mosqueId = $result['id'];
                $name = $result['name'];
                $isAdr = array_keys($result);
                if(in_array('vicinity', $isAdr)){
                    $address = $result['vicinity'];
                }else{
                     $address = "N/A";
                }

                $lat = $result['geometry']['location']['lat'];
                $lng = $result['geometry']['location']['lng'];
                $data[] = array('name'=>$name, 'id'=>$mosqueId, 'lat'=>$lat, 'lng'=>$lng, 'address'=>$address);
                //$cord[]=$lat.','.$lng;
            }
        }
 
        $status = true;
        $msg = 'successful results';
        return response()->json(['msg'=>$msg, 'status'=>$status,"no. of results"=>$resultCount,'response'=>$data]);
//        return response()->json([$response_a['status']]);
    }
    
 //////////////////////////////////////////////////////////////////////////////   
/////////////////////////////////Register//////////////////////////////////////
    public function register(Request $request){
        $parameters = $request->only('name', 'email', 'password');
        $user_exists = User::where('email',$parameters['email'])->count();
        if($user_exists>0){
            $status=false;
            $message='User already exists';
            return response()->json(['status'=>$status, 'msg'=>$message]);
        }
        
        if(!empty($parameters['email']) and !empty($parameters['password'])){
            $user = new User;
            $user->name = $parameters['name'];
            $user->email =  $parameters['email']; // optional
            $user->password = Hash::make($parameters['password']); 

            $user->save();
            $user_id = $user->id;
//            $path = public_path() .'/images/'.$user_id;
//            File::makeDirectory($path);
//            File::makeDirectory($path.'/feed_images');
//            File::makeDirectory($path.'/feed_videos');
//            File::makeDirectory($path.'/feed_thumbs');
            $status=true;
            $message='User created successfully';
            $email=$user->email;
            $password=$user->password;
            //return $this->signin($email,$password);

            return response()->json(['status'=>$status, 'msg'=>$message]);
        }
    }
    
 ////////////////////Login///////////////////////////////////   
    public function login(Request $request){
        
        $credentials = $request->only('email','password');
        $params = $request->only('device_token');
        $emailId = $credentials['email'];
        $deviceToken = $params['device_token'];

        try{
             if (! $token=JWTAuth::attempt($credentials)) {
                 $status=false;
                 $message='Credentials could not be authorized';
                 return response()->json(['status'=>$status, 'msg'=>$message]);
                // return $this->response->errorUnauthorized();
             }
        }catch(JWTException $ex){
                 $status=false;
                 $message='Some Internal error occurred';
                 return response()->json(['status'=>$status, 'msg'=>$message]);
                 //return $this->response->errorInternal();
        }
        $status=true;
        $message='Login Successfully';
        $user=User::where('email',$emailId)->first(['name', 'email', 'profile_picture', 'id']);
        
        Token::where('device_token',$deviceToken)->forceDelete();
        
        $newToken = new Token;
        $newToken->user_id = $user->id;
        $newToken->device_token =$deviceToken;
        $newToken->save();
//        $name=$user->name;
//        $email=$user->email;
//        $id=$user->id;
        return response()->json(['status'=>$status, 'msg'=>$message,'token'=>$token, 'user'=>$user]);
       // return $this->response->array(compact('token'))->setStatusCode(200);
        
    }

////////////////////////logout////////////////////////////////
    public function logout(Request $request){
        $params = $request->only('device_token');
        JWTAuth::invalidate(JWTAuth::getToken());
        
        Token::where('device_token', $params['device_token'])->forceDelete();
        $status=true;
        $message='Logged out successfully';
        return response()->json(['status'=>$status, 'msg'=>$message]);
    }
    
////////////////////profile picture update///////////////////////
    public function updateProfile(Request $request){
        $parameters = $request->only('picture', 'name', 'email');
        $hasPicture = $parameters['picture'];
        $hasName = $parameters['name'];
        $hasEmail = $parameters['email'];
        $id=$this->getLoggedInUser()->id;
        $imageName = null;
        //image update
        if($hasPicture!=""){
            $imageName = $id.'_'.md5(time().uniqid()).'.jpeg';
            $destinationPath = public_path() .'/images/'.$id.'/';
            file_put_contents($destinationPath.$imageName, base64_decode($parameters['picture']));
            User::where('id',$id)->update(['profile_picture'=>'/images/'.$id.'/'.$imageName]);
        }
        //name update
        if($hasName!=""){
            User::where('id',$id)->update(['name'=>$hasName]);
        }
        //email update
        if($hasEmail!=""){
            $checkEmail = User::where('email',$hasEmail)->get(['id']);
            if($checkEmail[0]['id'] != $id){
                $status=false;
                $message='email provided is already assigned to someone else';
                return response()->json(['status'=>$status, 'msg'=>$message]);
            }
            User::where('id',$id)->update(['email'=>$hasEmail]);
        }
        
       // $empty=empty($hasEmail);
        
        $status=true;
        $message='profile updated successfully';
        return response()->json(['status'=>$status,'msg'=>$message, 'url'=>'/images/'.$id.'/'.$imageName]);
}
    
////////////////////////update password/////////////////////////
    public function updatePassword(Request $request){
        $parameters= $request->only('password','newPassword');
        $id=$this->getLoggedInUser()->id;
        $pass=$this->getLoggedInUser()->password;
        $password = $parameters['password'];
        $hasPassword = $parameters['newPassword'];
        $emailPassword = User::where('id',$id)->get(['email','password']);
        $credentials = array('email'=>$emailPassword[0]['email'],'password'=>$emailPassword[0]['password']);
        
       // $credentials['email'] = $credential[0]['email'];
       // $credentials['password'] = $credentials[0]['password'];
       // return $credentials;
        //return response()->json(['credentials'=>$credentials[0]['password'],'password_provided'=>$password]);
        
        if($hasPassword!=""){
            $newPassword = Hash::make($hasPassword);
        }
        
        //return response()->json(['auth'=>Hash::check($password,$pass)]);
        
        if (Hash::check($password,$pass) and $hasPassword!="") {
            $newPassword = Hash::make($hasPassword);
            User::where('id',$id)->update(['password'=>$newPassword]);
            $status=true;
            $message='password updated successfully';
            return response()->json(['status'=>$status,'msg'=>$message]);
                // return $this->response->errorUnauthorized();
             }
            
        else{
        $status=false;
        $message='field is empty or invalid authorization';
        return response()->json(['status'=>$status,'msg'=>$message]);
        }
    }
    
/////////////////////////Searching User////////////////////////////////////
    public function searchUser($search){
        
        if ($search==null){
             $status=false;
            $message='search is empty';
            return response()->json(['status'=>$status, 'msg'=>$message]);
        }
        
        $id = $this->getLoggedInUser()->id;
       // $search = $parameters['search'];
//        $searched = User::where('id','!=',$id)->where('name','like','%'.$search.'%')->orWhere('mobile','like','%'.$search.'%')->orWhere('email','like','%'.$search.'%')->get();
        $searched = User::where('id','!=',$id)
            ->where(function ($query) {
                global $search;
                $query->where('name','like','%'.$search.'%')->orWhere('mobile','like','%'.$search.'%')->orWhere('email','like','%'.$search.'%');
            })->get();
        $status=true;
        $message='data successfully retrieved';
        return response()->json(['status'=>$status,'msg'=>$message,'search'=>$searched]);
    }

///////////////////////////Follow Unfollow///////////////////////
    public function getFollowUnfollow($userId){
        $followId = $userId;
        $id = $this->getLoggedInUser()->id;
        
        $query= Follow::where('follower_id',$id)->where('followed_id',$followId)->count();
        $followers=Follow::where('followed_id',$userId)->count();
        $followings=Follow::where('follower_id',$userId)->count();
        if ($query < 1){
            $status=true;
            $message='you are not following this id';
            $follow=false;
            return response()->json(['status'=>$status, 'msg'=>$message, 'follow'=>$follow, 'followers'=>$followers,'followings'=>$followings]);
        }
        $status=true;
        $message='You are following this Id';
        $follow=true;
        return response()->json(['status'=>$status, 'msg'=>$message, 'follow'=>$follow, 'followers'=>$followers, 'followings'=>$followings]);
    }
    
/////////////////////////true Following and Unfollowing functions/////////////////////////
    public function followUnfollow(Request $request){
        $followedId = $request->only('id');
        $id = $this->getLoggedInUser()->id;
        $followId= $followedId['id'];
        $check = Follow::where('followed_id',$followId)->where('follower_id',$id)->count();
        if ($check > 0){
            Follow::where('followed_id',$followId)->where('follower_id',$id)->delete();
            $status=true;
            $isFollowing=false;
            $message='unfollowing successful';
            return response()->json(['status'=>$status, 'msg'=>$message, 'isFollowing'=>$isFollowing]);
        }else{
            $follow = new Follow;
            $follow->follower_id = $id;
            $follow->followed_id = $followId; // optional
            $follow->save();
            $status=true;
            $isFollowing=true;
            $message='following successful';
            
            if($followedId != $id){
                $notification = new Notification;
                $notification->type = $notification->types['follow'];
                $notification->user_id = $followedId['id'];
                $notification->relation_user_id = $id;
                $notification->save();
                 
                $tokens = Token::where('user_id', $notification->user_id)->get(['device_token']);
            
                $deviceTokens = array();
                foreach($tokens as $token){
                        array_push($deviceTokens, $token->device_token);
                }


                $notification->sendNotification("Test Message Hello",$deviceTokens);
            }
            
            
            
            return response()->json(['status'=>$status, 'msg'=>$message, 'isFollowing'=>$isFollowing]);
        }
    }
    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////// masjidy-fetching prayer time ////////////////////////////////////////
    
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
    
  /////////////////////////////////This function has been discarded/////////////////////////////////
                                    ////Not in use anymore/////
    public function signin($email,$password){
        
        $credentials = array('email'=>$email,'password'=>$password);
        $emailId = $credentials['email'];
        

        try{
             if (! $token=JWTAuth::attempt($credentials)) {
                 $status=false;
                 $message='Credentials could not be authorized';
                 return response()->json(['status'=>$status, 'msg'=>$message]);
                // return $this->response->errorUnauthorized();
             }
        }catch(JWTException $ex){
                 $status=false;
                 $message='Some Internal error occurred';
                 return response()->json(['status'=>$status, 'msg'=>$message]);
                 //return $this->response->errorInternal();
        }
        $status=true;
        $message='Login Successfully';
        $user=User::where('email',$emailId)->first();
        $name=$user->name;
        return response()->json(['status'=>$status, 'msg'=>$message,'token'=>$token,'name'=>$name]);
       // return $this->response->array(compact('token'))->setStatusCode(200);
        
    }
    
////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////
    
    public function attachPermission(Request $request){
        $parameters = $request->only('permission','role');
        $permissionParam=$parameters['permission'];
        $roleParam=$parameters['role'];
        
        $role =Role::where('name',$roleParam)->first();
        
        $permission=Permission::where('name',$permissionParam)->first();
        
        $role->attachPermission($permission);
        
        return $this->response->created();
        
    }
    
    public function getPermission($roleParam){
        $role=Role::where('name', $roleParam)->first();
        return $this->response->array($role->perms);
    }
}
