<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function matches(Request $request){
        $database = app('firebase.database');
        $matches = $database->getReference('Users/'.$request['uID'].'/connections/matches')->getSnapshot();
        $users = [];
        if($matches->exists()){
            $matches = $database->getReference('Users/'.$request['uID'].'/connections/matches')->getChildKeys();
            foreach($matches as $uId){
                $chatId = $database->getReference('Users/'.$request['uID'].'/connections/matches/'.$uId)->getChild('chatId')->getValue();
                $matchUser = $database->getReference('Users/'.$uId)->getSnapshot();
                $users[$uId]['name'] = $matchUser->getChild('name')->getValue();
                $users[$uId]['profileImageUrl'] = $matchUser->getChild('profileImageUrl')->getValue(); 
                $users[$uId]['chatId'] = $chatId; 
            }
        }
        return $users;
    }

    public function chat(Request $request){
        $database = app('firebase.database');
        $snapshot = $database->getReference('Chat/'.$request['id'])->getSnapshot();
        $texts = [];
        if(!$snapshot->exists())
            return $texts;
        $chats = $database->getReference('Chat/'.$request['id'])->getValue();
        foreach($chats as $key => $val){
            array_push($texts, [$val['createdByUser'] => $val['text']]);
        }
        return $texts;
    }
}
