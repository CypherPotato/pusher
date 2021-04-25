@extends('layouts.app')

@section('content')

<div class="container h-100">
    <div class="row justify-content-center">
        <div class="col-12 mt-3">
            <div id="accordion">
                <div class="card mb-2 shadow border-0">
                    <div class="card-header">
                        Public key editor
                    </div>
                    <div class="card-body collapse show" id="block1">
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-7">
                                <label for="hashLabel">Private key <span class="text-danger">(don't share this key!)</span></label>
                                <input class="form-control bg-white" id="hashLabel" type="text" readonly value="{{$hash}}">
                            </div>
                            <div class="form-group col-sm-12 col-md-3">
                                <label for="endpoint">Endpoint</label>
                                <input class="form-control bg-white" id="endpoint" type="text" readonly value="https://{{$hostname}}/getPublicKey">
                            </div>
                            <div class="form-group col-sm-12 col-md-2">
                                <label for="endpoint">Method</label>
                                <input class="form-control bg-white" id="endpoint" type="text" readonly value="GET">
                            </div>
                            <div class="form-group col-sm-12 col-md-7">
                                <label for="hashLabel">Public key</label>
                                <input class="form-control bg-white" id="hashLabel" type="text" readonly value="{{$public_key ?? "Will be generated after saving the content"}}">
                            </div>
                            <div class="form-group col-sm-12 col-md-5">
                                <label for="hashLabel">Salt</label>
                                <input class="form-control bg-white" id="hashLabel" type="text" readonly value="{{$salt}}">
                            </div>
                            <div class="form-group col-sm-12 col-md-12">
                                <span>Instruções para uso de API:</span>
                                <ul class="mb-0">
                                    <li><code>public_key</code> - the key for getting the message</li>
                                </ul>
                            </div>
                        </div>
                        <form action="{{route('createKeyPair')}}">
                            <input hidden type="text" name="hash" value="{{$hash}}">
                            <input hidden type="text" name="public_key" value="{{$public_key ?? "-1"}}">
                            <div class="form-group">
                                <label for="exampleFormControlFile1">Public key message content</label>
                                <textarea onkeydown="if(event.keyCode===9){var v=this.value,s=this.selectionStart,e=this.selectionEnd;this.value=v.substring(0, s)+'\t'+v.substring(e);this.selectionStart=this.selectionEnd=s+1;return false;}"
                                 class="form-control" style="min-height: 256px; font-family: monospace;" maxlength="2048" id="message" name="message">{{$text ?? ""}}</textarea>
                            </div>
                            @if(\Session::has("message"))
                                <span>
                                    {{\Session::get("message")}}
                                </span>
                            @endif
                            <div class="form-group d-flex">
                                <a href="{{route('ViewMessages', ['hash' => $hash])}}" class="btn btn-outline-primary mr-2">Cancel</a>
                                <div class="ml-auto">
                                    @if(isset($public_key))
                                    <a href="{{route('DeleteKeyPair', ['hash' => $hash, 'public_key' => $public_key])}}" class="btn btn-outline-danger mr-2">Delete this public key</a>
                                    @endif
                                    <button type="submit" class="btn btn-primary pusher-bg">{{isset($public_key) ? 'Edit' : 'Create'}} public key</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
