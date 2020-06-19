<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PushMessage;

class PushController extends Controller
{
    public static function ViewMessages(Request $request) {
        if($request->id == null) return back()->with("message", "Identificação é obrigatória.");
        $hash = hash('sha256', $request->id . $request->privateKey);

        $messages = PushMessage::where('private_key', $hash)->orderBy("created_at", "DESC")->paginate(20);
        $messages->setPath($request->fullUrl());

        return view('viewMessages', ["messages" => $messages, "hash" => $hash, "hostname" => $_SERVER['SERVER_NAME']]);
    }

    public static function Push(Request $request) {
        $pmsg = new PushMessage;

        if($request->private_key == null) return response()->json(["success" => false, "message" => "Private key not provided."], 400);
        if($request->subject == null) return response()->json(["success" => false, "message" => "Subject not provided."], 400);
        if($request->message == null) return response()->json(["success" => false, "message" => "Message not provided."], 400);
        if(strlen($request->private_key) != 64) return response()->json(["success" => false, "message" => "Private key is invalid."], 400);
        if(!ctype_xdigit($request->private_key)) return response()->json(["success" => false, "message" => "Private key is invalid."], 400);
        if(strlen($request->message) >= 2048) return response()->json(["success" => false, "message" => "Message length is too big (>2048)."], 400);
        if(strlen($request->subject) >= 512) return response()->json(["success" => false, "message" => "Subject length is too big (>512)."], 400);

        $pmsg->private_key = $request->private_key;
        $pmsg->subject = $request->subject;
        $pmsg->message = $request->message;

        $pmsg->save();
        return response()->json(["success" => true, "message" => "Message inserted.", "data" => $request->toArray()], 200);
    }
}
