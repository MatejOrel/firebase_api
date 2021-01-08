<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function matches(){
        $database = app('firebase.database');
        $matches = $database->getReference('Users/'.auth()->user()->getAuthIdentifier().'/connections/matches')->getSnapshot();
        $users = [];
        if($matches->exists()){
            $matches = $database->getReference('Users/'.auth()->user()->getAuthIdentifier().'/connections/matches')->getChildKeys();
            foreach($matches as $uId){
                $chatId = $database->getReference('Users/'.auth()->user()->getAuthIdentifier().'/connections/matches/'.$uId)->getChild('chatId')->getValue();
                $matchUser = $database->getReference('Users/'.$uId)->getSnapshot();
                $users[$uId]['name'] = $matchUser->getChild('name')->getValue();
                $users[$uId]['profileImageUrl'] = $matchUser->getChild('profileImageUrl')->getValue(); 
                $users[$uId]['chatId'] = $chatId; 
            }
        }
        return view('matches', compact('users'));
    }

    public function chat(Request $request){
        $database = app('firebase.database');
        $chats = $database->getReference('Chat/'.$request->input('id'))->getValue();
        $texts = [];
        foreach($chats as $key => $val){
            array_push($texts, [$val['createdByUser'] => $val['text']]);
        }
        return view('chat', compact('texts'));
    }
}
