<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SwipeController extends Controller
{
    public function isMatch(String $id){
        $database = app('firebase.database');
        $currentUserConnections =  $database->getReference('Users/'.auth()->user()->getAuthIdentifier().'/connections/yeps/')->getSnapshot();
        if($currentUserConnections->hasChild($id)){
            $key = $database->getReference()->getChild('Chat')->push()->getKey();
            $database->getReference('Users/'.auth()->user()->getAuthIdentifier().'/connections/matches/'.$id.'/chatId')->set($key);
            $database->getReference('Users/'.$id.'/connections/matches/'.auth()->user()->getAuthIdentifier().'/chatId')->set($key);
        }
    }

    public function left(Request $request){
        $database = app('firebase.database');
        $database->getReference('Users/'.$request->input('id').'/connections/nope/'.auth()->user()->getAuthIdentifier())
        ->set(
            date("Y-m-d")
        );
        $database->getReference('Users/'.$request->input('id').'/connections/yeps/'.auth()->user()->getAuthIdentifier())->remove();
    }

    public function right(Request $request){
        $database = app('firebase.database');
        $database->getReference('Users/'.$request->input('id').'/connections/yeps/'.auth()->user()->getAuthIdentifier())
        ->set(
            date("Y-m-d")
        );
        $database->getReference('Users/'.$request->input('id').'/connections/nope/'.auth()->user()->getAuthIdentifier())->remove();
        $this->isMatch($request->input('id'));
    }
}
