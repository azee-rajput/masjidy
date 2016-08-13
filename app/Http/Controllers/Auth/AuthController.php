<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTExceptions;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        $this->middleware('guest', ['except' => 'logout']);
//    }

    public function hello(){
        return "Hello";
    }
  public function authenticate(Request $request){
     
      $credentials = $request->only('email','password');
      
      try{
         if (! $token=JWTAuth::attempt($credentials)) {
             return $this->response->errorUnauthorized();
         }
      }catch(JWTException $ex){
      return $this->response->errorInternal();
    }
      return $this->response->array(compact('token'))->setStatusCode(200);
  }
    
    public function getAllFeeds($limit = 5, $offset = 0){
        $user = $this->getLoggedInUser();
        if($user != null){
            //$this->response->array(compact('user'))->setStatusCode(200);        
            return $user;
        }else{
            return "Hello WOrld";
        }
    }

    
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
    
    public function index(){
        return User::all();
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
    
    public function getToken(){
        $token=JWTAuth::getToken();
        if(! $token){
            return $this->response->errorUnauthorized("Token is invalid");
        }
        try{
            $refreshedToken=JWTAuth::refresh($token);
        } catch (JWTException $ex){
            $this->response->error('something went wrong');
        }
        return $this->response->array(compact('refreshedToken'));
    }
    
    public function destroy(Request $request){
        $user=JWTAuth::parseToken()->authenticate();
        
        if(! $user){
            
        }
        $user->delete();
    }
}
