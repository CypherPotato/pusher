<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PushMessage;
use App\KeyPair;

class PushController extends Controller
{
    public static function GetPublicKeyContent(Request $request) {
        if($request->public_key == null) return response()->json(['message' => 'Invalid public key.'], 400);

        $newKeyPair = KeyPair::where('public_key', $request->public_key)->first();
        if($newKeyPair == null) {
            return response()->json(['message' => 'Invalid public key.'], 404);;
        }

        if($request->raw == 'true') {
            return response($newKeyPair->text, 200);
        } else {
            return response()->json(['public_key' => $request->public_key, "text" => $newKeyPair->text], 200);
        }
    }

    public static function EditPublicKeyView(Request $request) {
        if($request->hash == null) return back()->with("message", "Requisição inválida: chave privada inválida.");
        if($request->public_key == null) return back()->with("message", "Requisição inválida: chave pública inválida.");

        $kp = KeyPair::where('public_key', $request->public_key)->first();

        return view('publicKeyEditor', [
            "hostname" => $_SERVER['SERVER_NAME'],
            "hash" => $request->hash,
            "text" => $kp->text,
            "public_key" => $request->public_key
        ]);
    }

    public static function CreatePublicKeyView(Request $request) {
        if($request->hash == null) return back()->with("message", "Requisição inválida: chave privada inválida.");

        return view('publicKeyEditor', [
            "hostname" => $_SERVER['SERVER_NAME'],
            "hash" => $request->hash
        ]);
    }

    public static function CreateKeyPair(Request $request) {
        if($request->message == null) return back()->with("message", "Mensagem do bloco de chave pública é necessária.");
        if($request->hash == null) return back()->with("message", "Requisição inválida: chave privada inválida.");

        if($request->public_key == "-1") {
            $newKeyPair = new KeyPair;
            $newKeyPair->text = $request->message;
            $newKeyPair->private_key = $request->hash;
            $newKeyPair->public_key = hash('sha256', $request->hash . $request->message . rand());
        } else {
            $newKeyPair = KeyPair::where('public_key', $request->public_key)->first();
            if($newKeyPair == null) {
                return back()->with("message", "Requisição inválida: chave pública inválida.");
            }
            $newKeyPair->text = $request->message;
        }

        $newKeyPair->save();

        return redirect(route('ViewMessages', ["hash" => $request->hash]));
    }

    public static function DeleteKeyPair(Request $request) {
        if($request->hash == null) return back()->with("message", "Requisição inválida: chave privada inválida.");
        if($request->public_key == null) return back()->with("message", "Requisição inválida: chave pública inválida.");

        $newKeyPair = KeyPair::where('public_key', $request->public_key)->first();
        if($newKeyPair == null) {
            return back()->with("message", "Requisição inválida: chave pública inválida.");
        }
        $newKeyPair->forceDelete();

        return redirect(route('ViewMessages', ["hash" => $request->hash]));
    }

    public static function ViewMessages(Request $request) {
        if($request->has("hash")) {
            $hash = $request->hash;
        } else {
            if($request->id == null) return back()->with("message", "Identificação é obrigatória.");
            $hash = hash('sha256', $request->id . $request->privateKey);
            return redirect(route('ViewMessages', ["hash" => $hash])); // esconde o login e senha no login
        }

        $messages = PushMessage::where('private_key', $hash)->orderBy("created_at", "DESC")->paginate(20, ['*'], 'messages');
        $messages->setPath($request->fullUrl());

        $keys = KeyPair::where('private_key', $hash)->orderBy("created_at", "DESC")->paginate(20, ['*'], 'keys');
        $keys->setPath($request->fullUrl());

        return view('viewMessages', ["messages" => $messages, 'public_keys' => $keys, "hash" => $hash, "hostname" => $_SERVER['SERVER_NAME']]);
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
