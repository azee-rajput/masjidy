<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
define( 'API_ACCESS_KEY', 'AIzaSyAlEfssHn1kQa_jYvA0Mc6R_L1OHESCXe4' );
class Notification extends Model
{
    //
    protected $table = 'notifications';
    public $types = array('like' => 1, 'comment' => 2, 'share' => 3, 'follow' => 4);
    
    public function sendNotification($msg, $registrationIds){
        // API access key from Google API's Console
        
        //$registrationIds = array('dNfPrtfNpnE:APA91bGUa04Yb1Rd1nO8L5a3s_5eVepeqWG19v5BdSAuRN8eFo1mFS5TOZFSgsUlWVzy2vD2qJ-ci7_2J7w4GkfUeoORVp0SHP5oN6w3Kkq_q-uR3jA6o3mnBjWhvcAYHUJ-gt5llDrb');
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
            'registration_ids' 	=> $registrationIds,
            //'to' => 'dNfPrtfNpnE:APA91bGUa04Yb1Rd1nO8L5a3s_5eVepeqWG19v5BdSAuRN8eFo1mFS5TOZFSgsUlWVzy2vD2qJ-ci7_2J7w4GkfUeoORVp0SHP5oN6w3Kkq_q-uR3jA6o3mnBjWhvcAYHUJ-gt5llDrb',
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
       
    }
}
