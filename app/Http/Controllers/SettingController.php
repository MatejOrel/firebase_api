<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function userData(Request $request){
        try{
            $database = app('firebase.database');
            $uID = $request['uID'];
            $snapshot = $database -> getReference('Users/'.$uID)->getSnapshot();
            if(!$snapshot->hasChildren())
                return response('failed', 422);
            $keys = $database -> getReference('Users/'.$uID) -> getChildKeys();
            $values = [];
            $values['uId'] = $uID;
            foreach ($keys as $attr){
                $values[$attr] = $database -> getReference('Users/'.$uID.'/'.$attr) -> getValue();
            }
            return $values;
        }
        catch(Exception $e){
            return response('failed', 422);
        }
    }

    public function saveData(Request $request){
        $userProperties = [
            'name' => $request['name'],
            'phone' => $request['phone'],
            'bio' => $request['bio'],
            'dateOfBirth' => $request['dateOfBirth'],
            'showSex' => $request['showSex'],
            'minAge' => $request['minAge'],
            'maxAge' => $request['maxAge'],
            'distance' => $request['distance']
         ];
        $database = app('firebase.database');
        $database->getReference('Users/'.$request['uId'].'/')
         ->update(
             [
                'name' => $userProperties['name'],
                'phone' => $userProperties['phone'],
                'bio' => $userProperties['bio'],
                'dateOfBirth' => $userProperties['dateOfBirth'],
                'showSex' => $userProperties['showSex'],
                'minAge' => $userProperties['minAge'],
                'maxAge' => $userProperties['maxAge'],
                'distance' => intval($userProperties['distance'])
             ]);
    }
}
