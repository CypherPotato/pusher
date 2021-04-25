<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\PushMessage;
use App\KeyPair;
use App\TelegramBot;

class PushController extends Controller
{
    public static function DeleteTelegramBot(Request $request) {
        if($request->public_key == null) return back()->with("message", "Invalid request: no public key provided.");

        $tbot = TelegramBot::where('public_key', $request->public_key)->first();
        if($tbot == null) {
            return back()->with("message", "No telegram bot was associed with this private key.");
        } else {
            $tbot->forceDelete();
            return back()->with("message", "Telegram bot successfully removed.");
        }
    }

    public static function AssignTelegramBot(Request $request) {
        if($request->public_key == null) return back()->with("message", "Invalid request: no public key provided.");
        if($request->token == null) return back()->with("message", "Invalid request: no token provided.");

        // ping token
        $token = $request->token;
        $response = Http::get("https://api.telegram.org/bot$token/getUpdates");

        if(json_decode($response)->ok == true) {
            $tbot = TelegramBot::where('public_key', $request->private_key)->first();
            if($tbot == null) {
                $tbot = new TelegramBot;
            }

            $tbot->public_key = $request->public_key;
            $tbot->token = $token;

            $tbot->save();

            return back()->with("message", "Token successfully associed to this private key. All incoming messages will be redirected to the Telegram bot also.");
        } else {
            return back()->with("message", "Cannot ping this Telegram token.");
        }
    }

    public static function GetPublicKeyContent(Request $request) {
        if($request->public_key == null) return response()->json(['message' => 'Missing public key.'], 400);

        $newKeyPair = KeyPair::where('public_key', $request->public_key)->first();
        if($newKeyPair == null) {
            return response()->json(['message' => 'Invalid public key.'], 404);;
        }

        return response()->json(['public_key' => $request->public_key, "salt" => hash('md5', $newKeyPair->private_key), "text" => $newKeyPair->text], 200);
    }

    public static function EditPublicKeyView(Request $request) {
        if($request->hash == null) return back()->with("message", "Invalid request: invalid private_key (hash).");
        if($request->public_key == null) return back()->with("message", "Invalid request: no public key provided.");

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
        if($request->hash == null) return back()->with("message", "Invalid request: no private_key provided..");
        if(strlen($request->hash) != 64) return response()->json(["success" => false, "message" => "Private key is invalid."], 400);
        if(!ctype_xdigit($request->hash)) return response()->json(["success" => false, "message" => "Private key is invalid."], 400);

        $salt = hash('md5', $request->hash);

        return view('publicKeyEditor', [
            "hostname" => $_SERVER['SERVER_NAME'],
            "hash" => $request->hash,
            "salt" => $salt
        ]);
    }

    public static function CreateKeyPair(Request $request) {
        if($request->method() == "GET") {
            if($request->message == null) return back()->with("message", "Param 'message' is missing.");
            if($request->hash == null) return back()->with("message", "Param 'hash' is missing.");
        } else {
            if($request->message == null) return response()->json(["message" => "Param 'message' is missing."], 400);
            if($request->private_key == null) return response()->json(["message" => "Param 'private_key' is missing"], 400);
        }

        $public_key = "";
        if($request->public_key == "-1" || $request->public_key == null) {
            $public_key = hash('sha256', $request->hash . $request->message . rand());
            $newKeyPair = new KeyPair;
            $newKeyPair->text = $request->message;
            $newKeyPair->private_key = $request->hash ?? $request->private_key;
            $newKeyPair->public_key = $public_key;
        } else {
            $public_key = $request->public_key ?? "";
            $newKeyPair = KeyPair::where('public_key', $request->public_key)->first();
            if($newKeyPair == null && $request->public_key != null) {
                if($request->method() == "GET") {
                    return back()->with("message", "Invalid request: invalid private_key.");
                } else {
                    return response()->json(["message" => "Invalid request: invalid public_key."], 400);
                }
            } else {
                $newKeyPair->text = $request->message;
            }
        }

        $newKeyPair->save();

        if($request->method() == "GET") {
            return redirect(route('ViewMessages', ["hash" => $request->hash]));
        } else {
            return response()->json(["message" => "Public key " . ($request->public_key == null ? 'created' : 'edited') . " successfully.", "public_key" => $public_key, "text" => $request->text], 200);
        }
    }

    public static function DeleteMessage(Request $request) {
        if($request->id == null) return back()->with("message", "Error: ID was not provided.");
        if($request->hash == null) return back()->with("message", "Error: hash wasn't provided.");

        $msg = PushMessage::where('id', $request->id)->first();

        if($msg == null) return back()->with("message", "Error: message not found.");
        if($msg->public_key != $request->hash) return back()->with("message", "Error: message private key doens't match with provided key.");

        $msg->delete();
        return back();
    }

    public static function DeleteKeyPair(Request $request) {
        if($request->method() == "GET") {
            if($request->public_key == null) return back()->with("message", "Invalid request: missing 'public_key'.");
            if($request->hash == null) return back()->with("message", "Invalid request: invalid private key.");
        } else {
            if($request->public_key == null) return response()->json(["message" => "Invalid request: public_key not provided."], 400);
            if($request->private_key == null) return response()->json(["message" => "Invalid request: private_key not provided."], 400);
        }

        $newKeyPair = KeyPair::where('public_key', $request->public_key)->first();
        if($newKeyPair == null) {
            if($request->method == "GET") {
                return back()->with("message", "Invalid public_key.");
            } else {
                return response()->json(["message" => "Invalid request: invalid or deleted public_key."], 400);
            }
        }
        if($newKeyPair->private_key != ($request->hash ?? $request->private_key)) {
            return response()->json(["message" => "Error: message private key doens't match with provided key."], 400);
        }
        $newKeyPair->forceDelete();

        if($request->method() == "GET") {
            return redirect(route('ViewMessages', ["hash" => $request->hash]));
        } else {
            return response()->json(["message" => "Key successfully deleted.", "public_key" => $request->public_key], 200);
        }
    }

    public static function ViewMessages(Request $request) {
        if($request->has("hash")) {
            $hash = $request->hash;
        } else {
            if($request->id == null) return back()->with("message", "Error: no ID was provided.");
            $hash = hash('sha256', $request->id . $request->privateKey);
            return redirect(route('ViewMessages', ["hash" => $hash])); // esconde o login e senha no login
        }

        $salt = hash('md5', $hash);
        $public_key = hash('sha256', $hash);

        $messages = PushMessage::where('public_key', $public_key)->orderBy("created_at", "DESC")->get();
        $token = TelegramBot::where('public_key', $public_key)->first();

        $keys = KeyPair::where('private_key', $hash)->orderBy("created_at", "DESC")->paginate(20, ['*'], 'keys');
        $keys->setPath($request->fullUrl());

        return view('viewMessages', ["messages" => $messages, 'public_key' => $public_key, 'telegramBot' => $token, 'salt' => $salt, 'public_keys' => $keys, "hash" => $hash, "hostname" => $_SERVER['SERVER_NAME']]);
    }

    public static function Push(Request $request) {
        $pmsg = new PushMessage;

        if($request->public_key == null) return response()->json(["success" => false, "message" => "Public key not provided."], 400);
        if($request->subject == null) return response()->json(["success" => false, "message" => "Subject not provided."], 400);
        if($request->message == null) return response()->json(["success" => false, "message" => "Message not provided."], 400);
        if(strlen($request->public_key) != 64) return response()->json(["success" => false, "message" => "Public key is invalid."], 400);
        if(!ctype_xdigit($request->public_key)) return response()->json(["success" => false, "message" => "Public key is invalid."], 400);

        $pmsg->public_key = $request->public_key;
        $pmsg->subject = $request->subject;
        $pmsg->message = $request->message;

        // send telegramMessage
        if(!isset($request->cancelTelegramMessage) && $request->cancelTelegramMessage != true) {
            $bot = TelegramBot::where('public_key', $request->public_key)->first();
            $token = $bot->token;
            if($bot != null) {
                $update = Http::get("https://api.telegram.org/bot$token/getUpdates");
                foreach(json_decode($update)->result as $result) {
                    $chatId = $result->message->chat->id;

                    $response = Http::get("https://api.telegram.org/bot$token/sendMessage", [
                        "chat_id" => $chatId,
                        "text" => "Pusher message\n[Subject]\n$request->subject\n\n[Text]\n$request->message"
                    ]);
                }
            }
        }

        $pmsg->save();
        return response()->json(["success" => true, "message" => "Message inserted.", "data" => $request->toArray()], 200);
    }
}
