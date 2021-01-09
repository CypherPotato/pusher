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

        return response()->json(['public_key' => $request->public_key, "salt" => hash('md5', $newKeyPair->private_key), "text" => $newKeyPair->text], 200);
    }

    public static function EditPublicKeyView(Request $request) {
        if($request->hash == null) return back()->with("message", "Requisição inválida: chave privada inválida.");
        if($request->public_key == null) return back()->with("message", "Requisição inválida: chave pública inválida.");

        $kp = KeyPair::where('public_key', $request->public_key)->first();

        $salt = hash('md5', $request->hash);

        return view('publicKeyEditor', [
            "hostname" => $_SERVER['SERVER_NAME'],
            "hash" => $request->hash,
            "text" => $kp->text,
            "public_key" => $request->public_key,
            "salt" => $salt
        ]);
    }

    public static function CreatePublicKeyView(Request $request) {
        if($request->hash == null) return back()->with("message", "Requisição inválida: chave privada inválida.");

        $salt = hash('md5', $request->hash);

        return view('publicKeyEditor', [
            "hostname" => $_SERVER['SERVER_NAME'],
            "hash" => $request->hash,
            "salt" => $salt
        ]);
    }

    public static function CreateKeyPair(Request $request) {
        if($request->method() == "GET") {
            if($request->message == null) return back()->with("message", "Mensagem do bloco de chave pública é necessária.");
            if($request->hash == null) return back()->with("message", "Requisição inválida: chave privada inválida.");
        } else {
            if($request->text == null) return response()->json(["message" => "Mensagem do bloco de chave pública é necessária."], 400);
            if($request->private_key == null) return response()->json(["message" => "Requisição inválida: chave privada inválida."], 400);
        }

        $public_key = "";
        if($request->public_key == "-1" || $request->public_key == null) {
            $public_key = hash('sha256', $request->hash . $request->message . rand());
            $newKeyPair = new KeyPair;
            $newKeyPair->text = $request->message ?? $request->text;
            $newKeyPair->private_key = $request->hash ?? $request->private_key;
            $newKeyPair->public_key = $public_key;
        } else {
            $public_key = $request->public_key ?? "";
            $newKeyPair = KeyPair::where('public_key', $request->public_key)->first();
            if($newKeyPair == null && $request->public_key != null) {
                if($request->method() == "GET") {
                    return back()->with("message", "Requisição inválida: chave pública inválida.");
                } else {
                    return response()->json(["message" => "Requisição inválida: chave pública inválida."], 400);
                }
            } else {
                if($request->method() == "GET") {
                    $newKeyPair->text = $request->message;
                } else {
                    $newKeyPair->text = $request->text;
                }
            }
        }

        $newKeyPair->save();

        if($request->method() == "GET") {
            return redirect(route('ViewMessages', ["hash" => $request->hash]));
        } else {
            return response()->json(["message" => "Chave " . ($request->public_key == null ? 'criada' : 'alterada') . " com sucesso.", "public_key" => $public_key, "text" => $request->text], 200);
        }
    }

    public static function DeleteMessage(Request $request) {
        if($request->id == null) return back()->with("message", "Erro: não foi providenciado o ID.");
        if($request->hash == null) return back()->with("message", "Erro: não foi providenciado a chave pública.");

        $msg = PushMessage::where('id', $request->id)->first();

        if($msg == null) return back()->with("message", "Erro: mensagem não encontrada.");
        if($msg->public_key != $request->hash) return back()->with("message", "Erro: chave pública não confere com a da mensagem.");

        $msg->delete();
        return back();
    }

    public static function DeleteKeyPair(Request $request) {
        if($request->method() == "GET") {
            if($request->public_key == null) return back()->with("message", "Requisição inválida: chave pública inválida.");
            if($request->hash == null) return back()->with("message", "Requisição inválida: chave privada inválida.");
        } else {
            if($request->public_key == null) return response()->json(["message" => "Requisição inválida: chave pública inválida."], 400);
            if($request->private_key == null) return response()->json(["message" => "Requisição inválida: chave privada inválida."], 400);
        }

        $newKeyPair = KeyPair::where('public_key', $request->public_key)->first();
        if($newKeyPair == null) {
            if($request->method == "GET") {
                return back()->with("message", "Requisição inválida: chave pública inválida.");
            } else {
                return response()->json(["message" => "Requisição inválida: chave pública inválida."], 400);
            }
        }
        $newKeyPair->forceDelete();

        if($request->method() == "GET") {
            return redirect(route('ViewMessages', ["hash" => $request->hash]));
        } else {
            return response()->json(["message" => "Chave excluída com sucesso.", "public_key" => $request->public_key], 200);
        }
    }

    public static function ViewMessages(Request $request) {
        if($request->has("hash")) {
            $hash = $request->hash;
        } else {
            if($request->id == null) return back()->with("message", "Identificação é obrigatória.");
            $hash = hash('sha256', $request->id . $request->privateKey);
            return redirect(route('ViewMessages', ["hash" => $hash])); // esconde o login e senha no login
        }

        $salt = hash('md5', $request->hash);
        $public_key = hash('sha256', $hash);

        $messages = PushMessage::where('public_key', $public_key)->orderBy("created_at", "DESC")->paginate(20, ['*'], 'messages');
        $messages->setPath($request->fullUrl());

        $keys = KeyPair::where('private_key', $hash)->orderBy("created_at", "DESC")->paginate(20, ['*'], 'keys');
        $keys->setPath($request->fullUrl());

        return view('viewMessages', ["messages" => $messages, 'public_key' => $public_key, 'salt' => $salt, 'public_keys' => $keys, "hash" => $hash, "hostname" => $_SERVER['SERVER_NAME']]);
    }

    public static function Push(Request $request) {
        $pmsg = new PushMessage;

        if($request->public_key == null) return response()->json(["success" => false, "message" => "Public key not provided."], 400);
        if($request->subject == null) return response()->json(["success" => false, "message" => "Subject not provided."], 400);
        if($request->message == null) return response()->json(["success" => false, "message" => "Message not provided."], 400);
        if(strlen($request->public_key) != 64) return response()->json(["success" => false, "message" => "Public key is invalid."], 400);
        if(!ctype_xdigit($request->public_key)) return response()->json(["success" => false, "message" => "Public key is invalid."], 400);
        if(strlen($request->message) >= 2048) return response()->json(["success" => false, "message" => "Message length is too big (>2048)."], 400);
        if(strlen($request->subject) >= 512) return response()->json(["success" => false, "message" => "Subject length is too big (>512)."], 400);

        $pmsg->public_key = $request->public_key;
        $pmsg->subject = $request->subject;
        $pmsg->message = $request->message;

        $pmsg->save();
        return response()->json(["success" => true, "message" => "Message inserted.", "data" => $request->toArray()], 200);
    }
}
