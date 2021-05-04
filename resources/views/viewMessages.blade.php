@extends('layouts.app')

@section('content')

<div class="container h-100">
    <div class="row justify-content-center">
        <a href="/" class="mr-auto">
            <img src="{{asset('img/pusher.png')}}" class="ml-4 mt-4" style="height: auto; width: 256px">
        </a>

        @if(\Session::has('message'))
        <div class="col-12 my-3">
            <div class="alert alert-primary">
                {{\Session::get('message')}}
            </div>
        </div>
        @endif
        <div class="col-12 mt-3 pb-5">
            <div id="accordion">
                <div class="card mb-2 shadow border-0">
                    <div class="card-header">
                        <button class="btn btn-link pusher-color" data-toggle="collapse" data-target="#block1" aria-expanded="true" aria-controls="block1">
                            Channel information
                        </button>
                    </div>
                    <div class="card-body collapse show" id="block1">
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-7">
                                <label for="hashLabel">Private key <span class="text-danger">(don't share!)</span></label>
                                <input class="form-control bg-white" id="hashLabel" type="text" readonly value="{{$hash}}">
                            </div>
                            <div class="form-group col-sm-12 col-md-3">
                                <label for="endpoint">Endpoint</label>
                                <input class="form-control bg-white" id="endpoint" type="text" readonly value="https://{{$hostname}}/push">
                            </div>
                            <div class="form-group col-sm-12 col-md-2">
                                <label for="endpoint">Method</label>
                                <input class="form-control bg-white" id="endpoint" type="text" readonly value="POST">
                            </div>
                            <div class="form-group col-sm-12 col-md-7">
                                <label for="hashLabel">Public key</label>
                                <input class="form-control bg-white" id="hashLabel" type="text" readonly value="{{$public_key}}">
                            </div>
                            <div class="form-group col-sm-12 col-md-5">
                                <label for="hashLabel">Salt</label>
                                <input class="form-control bg-white" id="hashLabel" type="text" readonly value="{{$salt}}">
                            </div>
                            <div class="form-group col-sm-12 col-md-12">
                                <span>Instruções para uso de API:</span>
                                <ul class="mb-0">
                                    <li><code>public_key</code> - use the generated key to send messages to this channel.</li>
                                    <li><code>subject</code> - the subject of the message.</li>
                                    <li><code>message</code> - the body of the message.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-2 shadow border-0">
                    <div class="card-header">
                        <button class="btn btn-link pusher-color" data-toggle="collapse" data-target="#block2" aria-expanded="true" aria-controls="block">
                            Dynamic messages
                        </button>
                    </div>
                    <div class="card-body collapse show" id="block2">
                        Reading messages from server...
                    </div>
                </div>
                <div class="card mb-2 shadow border-0">
                    <div class="card-header">
                        <button class="btn btn-link pusher-color" data-toggle="collapse" data-target="#block3" aria-expanded="true" aria-controls="block3">
                            Public keys
                        </button>
                    </div>
                    <div class="card-body collapse show" id="block3">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th scope="col" width="20%"><small><b>Date</b></small></th>
                                <th scope="col" width="50%"><small><b>Public key</b></small></th>
                                <th scope="col" width="25%" style="word-wrap: break-word;"><small><b>Text</b></small></th>
                                <th scope="col" width="5%"><small></small></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($public_keys as $key)
                                <tr>
                                    <td scope="col" width="20%"><small>{{$key->created_at->toString()}}<small></td>
                                    <td scope="col" width="50%"><code>{{$key->public_key}}<code></td>
                                    <td scope="col" width="25%" style="word-wrap: break-word;"><small>{{substr($key->text, 0, 44)}}<small></td>
                                    <th scope="col" width="5%"><a class="btn btn-sm btn-link" href="{{route('EditPublicKeyView', ['public_key' => $key->public_key, 'hash' => $hash])}}">Edit</a></th>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan=4 class="text-center pt-3">There's no public keys for this channel.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex w-100">
                            <a href="{{route('CreatePublicKey', ['hash' => $hash])}}" class="btn btn-primary pusher-bg ml-auto">
                                Create public key
                            </a>
                        </div>
                        {{ $public_keys->render() }}
                    </div>
                </div>
                <div class="card mb-2 shadow border-0">
                    <div class="card-header">
                        <button class="btn btn-link pusher-color" data-toggle="collapse" data-target="#block4" aria-expanded="true" aria-controls="block">
                            Telegram Bot Integration
                        </button>
                    </div>
                    <div class="card-body collapse show" id="block4">
                        <form action={{ route('assignTelegramBot') }}>
                            <div class="row">
                                <div class="col-sm-12 col-md-7">
                                    <label for="hashLabel">Telegram Bot Token</label>
                                    <input class="form-control bg-white" name="token" type="text" value="{{$telegramBot->token ?? ""}}">
                                    <input hidden name="public_key" value="{{ $public_key }}"/>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-auto">
                                    <button class="btn btn-primary pusher-bg">Assign token</button>
                                    <a href="{{ route('deleteTelegramBot', ['public_key' => $hash]) }}" class="ml-3 btn btn-outline-danger border-0">Delete token</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section("scripts")
<script>
$(document).ready(function() {
    $("#messagesTable").dataTable({
        "order": []
    });
    setInterval(function() {
        $.ajax({
            url: "{{ route('MessagesComponentCallback') }}",
            data: {
                public_key: '{{ $public_key }}',
                private_key: '{{ $hash }}'
            },
            success: function(response) {
                console.log(response);
                $('#block2').html(response);
            }
        })
    }, 3000);
});
</script>
@endsection