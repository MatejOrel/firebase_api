<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SwipeController extends Controller
{
    public function isMatch(String $id, String $uId){
        $database = app('firebase.database');
        $currentUserConnections =  $database->getReference('Users/'.$uId.'/connections/yeps/')->getSnapshot();
        if($currentUserConnections->hasChild($id)){
            $neki = $database->getReference('Chat/');
            $key = $database->getReference()->getChild('Chat')->push()->getKey();
            $database->getReference('Users/'.$uId.'/connections/matches/'.$id.'/chatId')->set($key);
            $database->getReference('Users/'.$id.'/connections/matches/'.$uId.'/chatId')->set($key);
        }
    }

    public function left(Request $request){
        try{
            $database = app('firebase.database');
            $neki = $database->getReference('Users/'.$request['id'].'/connections/nope/'.$request['uId']);
            $database->getReference('Users/'.$request['id'].'/connections/nope/'.$request['uId'])
            ->set(
                date("Y-m-d")
            );
            $database->getReference('Users/'.$request['id'].'/connections/yeps/'.$request['uId'])->remove();
            return response('success', 200);
        }
        catch(Exception $e){
            return response('failed', 422);
        }
    }

    public function right(Request $request){
        try{
            $database = app('firebase.database');
            $neki = $database->getReference('Users/'.$request['id'].'/connections/yeps/'.$request['uId']);
            $database->getReference('Users/'.$request['id'].'/connections/yeps/'.$request['uId'])
            ->set(
                date("Y-m-d")
            );
            $database->getReference('Users/'.$request['id'].'/connections/nope/'.$request['uId'])->remove();
            $this->isMatch($request['id'], $request['uId']);
            return response('success', 200);
        }
        catch(Exception $e){
            return response('failed', 422);
        }
    }
}
