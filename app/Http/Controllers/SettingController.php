<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function userData(Request $request){
        try{
            $database = app('firebase.database');
            $uID = $request->input('uID');
            $snapshot = $database -> getReference('Users/'.$uID)->getSnapshot();
            if(!$snapshot->hasChildren())
                return;
            $keys = $database -> getReference('Users/'.$uID) -> getChildKeys();
            $values = [];
            $values['uId'] = $uID;
            foreach ($keys as $attr){
                $values[$attr] = $database -> getReference('Users/'.$uID.'/'.$attr) -> getValue();
            }
            return response($values, 200);
        }
        catch(Exception $e){
            return response('failed', 422);
        }
    }

    public function saveData(Request $request){
        $userProperties = [
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'bio' => $request->input('bio'),
            'dateOfBirth' => $request->input('dateOfBirth'),
            'showSex' => $request->input('showSex'),
            'minAge' => $request->input('minAge'),
            'maxAge' => $request->input('maxAge'),
            'distance' => $request->input('distance')
         ];
        $database = app('firebase.database');
        $database->getReference('Users/'.auth()->user()->getAuthIdentifier().'/')
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

    public function saveUrl(Request $request){
        $database = app('firebase.database');
        $database->getReference('Users/'.auth()->user()->getAuthIdentifier().'/')
         ->update(
             [
                'profileImageUrl' => $request->url
             ]);
    }
}
