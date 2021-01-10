<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\SettingController;
use \Datetime;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   /* public function __construct()
    {
        $this->middleware('auth');
    }*/

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return;
    }

    public function findUsers(Request $request){
        try{
            $database = app('firebase.database');
            $uID = $request['uID'];
            $allUserId = $database->getReference('Users/')->getChildKeys();
            $settingController = new SettingController;
            $userData = $settingController->userData($request);
            if(!isset($userData['uId']))
                return response("User doesn't exist", 422);
            $users = [];
            foreach ($allUserId as $user){
                $snapshot = $database->getReference('Users/'.$user)->getSnapshot();
                if($snapshot->exists() &&
                    $user != $userData['uId'] &&
                    (!$snapshot->hasChild('/connections/nope/'.$userData['uId']) || date_diff(new DateTime($snapshot->getChild('connections')->getChild('nope')->getChild($userData['uId'])->getValue()), new DateTime())->format('%a') > 1) &&
                    (!$snapshot->hasChild('/connections/yeps/'.$userData['uId']) || date_diff(new DateTime($snapshot->getChild('connections')->getChild('yeps')->getChild($userData['uId'])->getValue()), new DateTime())->format('%a') > 1) &&
                    !$snapshot->hasChild('/connections/matches/'.$userData['uId']) &&
                    $snapshot->getChild('sex')->getValue() == $userData['showSex'] &&
                    $snapshot->getChild('dateOfBirth')->getValue() != null &&
                    date_diff(new DateTime($snapshot->getChild('dateOfBirth')->getValue()), new DateTime())->format('%y') >= $userData['minAge'] && 
                    date_diff(new DateTime($snapshot->getChild('dateOfBirth')->getValue()), new DateTime())->format('%y') <= $userData['maxAge'] &&
                    $this -> distance($userData['latitude'],$userData['longtitude'],$snapshot->getChild('latitude')->getValue(),$snapshot->getChild('longtitude')->getValue()) <= $userData['distance']){

                        $users[$user] = $snapshot->getValue();
                    }
            }
            return response($users, 200);
        }
        catch(Exception $e){
            return response('failed', 422);
        }
    }

    public function distance($lat1, $lon1, $lat2, $lon2) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
          return 0;
        }
        else {
          $theta = $lon1 - $lon2;
          $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
          $dist = acos($dist);
          $dist = rad2deg($dist);
          $miles = $dist * 60 * 1.1515;
      
        return ($miles * 1.609344);
        }
      }
}


