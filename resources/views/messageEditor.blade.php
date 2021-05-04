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
                                <input class="form-control bg-white" id="endpoint" type="text" readonly value="https://{{$hostname}}/api/message">
                            </div>
                            <div class="form-group col-sm-12 col-md-2">
                                <label for="endpoint">Method</label>
                                <input class="form-control bg-white" id="endpoint" type="text" readonly value="GET">
                            </div>
                            <div class="form-group col-sm-12 col-md-7">
                                <label for="hashLabel">Public key</label>
                                <input class="form-control bg-white" id="hashLabel" type="text" readonly value="{{$public_key}}">
                            </div>
                            <div class="form-group col-sm-12 col-md-5">
                                <label for="hashLabel">Message ID</label>
                                <input class="form-control bg-white" id="hashLabel" type="text" readonly value="{{$id}}">
                            </div>
                            <div class="form-group col-sm-12 col-md-12">
                                <span>Instruções para uso de API:</span>
                                <ul class="mb-0">
                                    <li><code>public_key</code> - the channel public key</li>
                                    <li><code>id</code> - the message ID</li>
                                </ul>
                            </div>
                        </div>
                        <form action="{{route('api.editmessage')}}" method="POST">
                            <input hidden type="text" name="hash" value="{{$hash}}">
                            <input hidden type="text" name="public_key" value="{{$public_key}}">
                            <input hidden type="text" name="id" value="{{$id}}">
                            <div class="form-group">
                                <label for="hashLabel">Subject</label>
                                <input class="form-control bg-white" type="text" name="subject" value="{{$subject}}">
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlFile1">Message content</label>
                                <textarea onkeydown="if(event.keyCode===9){var v=this.value,s=this.selectionStart,e=this.selectionEnd;this.value=v.substring(0, s)+'\t'+v.substring(e);this.selectionStart=this.selectionEnd=s+1;return false;}"
                                 class="form-control" style="min-height: 256px; font-family: monospace;" maxlength="2048" id="message" name="message">{{$message ?? ""}}</textarea>
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
                                    <a href="{{route('DeleteMessage', ['public_key' => $public_key, 'private_key' => $hash, 'id' => $id])}}" class="btn btn-outline-danger mr-2">Delete this message</a>
                                    @endif
                                    <button type="submit" class="btn btn-primary pusher-bg">Edit message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
