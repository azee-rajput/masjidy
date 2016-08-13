<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Mosques;
use App\Manager;
use App\Mosque;

class AdminController extends Controller
{
    
/////////////////////////////////////search mosque by name on google map//////////////////////////////////////////////
    public function searchMosque($keyword){
        
        $url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=$keyword&key=AIzaSyDI2XjZcpklM6z4dB2XrRmh0GLMDVmnXe0&type=mosque";
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
                if(in_array('formatted_address', $isAdr)){
                    $address = $result['formatted_address'];
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
 
    
////////////add searched mosque in database/////////////////////////
    public function addMosque(Request $request){
        $params = $request->only('name','gm_id', 'address', 'latitude', 'longitude');
        $check = Mosques::where('gm_id',$params['gm_id'])->count();
        if($check > 0){
            $status = false;
            $message = 'Mosque already exists';
            return response()->json(['status'=>$status, 'msg'=>$message]);
        }
        $mosque = new Mosques;
        $mosque->name = $params['name'];
        $mosque->address = $params['address'];
        $mosque->gm_id = $params['gm_id'];
        //$mosque->manager_id = $params['manager_id'];
        $mosque->latitude = $params['latitude'];
        $mosque->longitude = $params['longitude'];
        
        $mosque->save();
        $status = true;
        $msg = 'Mosque added successfully';
        return response()->json(['msg'=>$msg, 'status'=>$status]);
    }
    
/////////////////search mosques in database///////////////////////
    public function getMosque($search){
        $result = Mosques::where('name','like','%'.$search.'%')->get();
        $check = Mosques::where('name','like','%'.$search.'%')->count();
        if($check < 1){
            $status = false;
            $msg = 'no Mosque found';
            return response()->json(['status'=>$status,'msg'=>$msg,'result'=>$result]);
        }
        $status = true;
        $msg = 'Mosque retrieved successfully';
        return response()->json(['status'=>$status,'msg'=>$msg,'result'=>$result]);
    }
    
///////////////mosques of manager//////////////////////////////
    public function managerMosque($id){
        $result = Manager::where('manager_id',$id)->get(['mosque_id']);
        $response = Mosque::whereIn('id',$result)->get();
        $status = true;
        $msg = 'Mosques retrieved successfully';
        return response()->json(['status'=>$status,'msg'=>$msg,'result'=>$response]);
    }
    
////////////showing specific mosque details////////////////////
    public function mosqueDetail($id){
        $response = Mosque::where('id',$id)->get();
        $manager = Manager::where('mosque_id',$id)->get(['manager_id']);
        $manager_detail = User::where('id',$manager)->get();
        
        $status = true;
        $msg = 'Mosques retrieved successfully';
        return response()->json(['status'=>$status,'msg'=>$msg,'result'=>$response]);
    }

//////////////List of managers////////////////////////
    public function getManager(){
        $manager = Manager::all();
        $response = User::whereIn('id',$manager)->distinct()->get();
        $status = true;
        $msg = 'all users';
        return response()->json(['status'=>$status,'msg'=>$msg,'result'=>$response]);
    }
    
//////////////assigning maanger to a mosque/////////////////////////
    public function addManager(Request $request){
        $params = $request->only('man_id','mos_id');
        $check = Manager::where("manager_id",$params['man_id'])->where('mosque_id',$params['mos_id'])->count();
        if ($check > 0){
            $status = false;
            $msg = 'Manager already exists';
            return response()->json(['status'=>$status,'msg'=>$msg]);
        }
        
        $manager = new Manager;
        $manager->manager_id = $params['man_id'];
        $manager->mosque_id = $params['mosque_id'];
        $manager->save();
        $status = true;
        $msg = 'Manager added';
        return response()->json(['status'=>$status,'msg'=>$msg]);
        
    }
    
////////////////deleting a manager/////////////////////////////////
    public function deleteManager(Request $request){
        $params = $request->only('id');
        $check = Manager::where("manager_id",$params['man_id'])->where('mosque_id',$params['mos_id'])->count();
        if ($check < 1){
            $status = false;
            $msg = 'Manager dont exists';
            return response()->json(['status'=>$status,'msg'=>$msg]);
        }
        $response = Manager::where("manager_id",$params['man_id'])->delete();
        $status = true;
        $msg = 'Manager deleted';
        return response()->json(['status'=>$status,'msg'=>$msg]);
    }
}
