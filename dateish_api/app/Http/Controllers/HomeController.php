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
                    date_diff(new DateTime($snapshot->getChild('dateOfBirth')->getValue()), new DateTime())->format('%y') <= $userData['maxAge']){

                        $users[$user] = $snapshot->getValue();
                    }
            }
            return response($users, 200);
        }
        catch(Exception $e){
            return response('failed', 422);
        }
    }
}
