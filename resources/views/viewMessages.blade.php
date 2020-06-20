@extends('layouts.app')

@section('content')

<div class="container h-100">
    <div class="row justify-content-center">
        @if(\Session::has('message'))
        <div class="col-12 my-3">
            <div class="alert alert-primary">
                {{\Session::get('message')}}
            </div>
        </div>
        @endif
        <div class="col-12 mt-3">
            <div id="accordion">
                <div class="card mb-2">
                    <div class="card-header">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#block1" aria-expanded="true" aria-controls="block1">
                            Informações do canal de recibo de mensagens
                        </button>
                    </div>
                    <div class="card-body collapse show" id="block1">
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-7">
                                <label for="hashLabel">Chave privada</label>
                                <input class="form-control bg-white" id="hashLabel" type="text" readonly value="{{$hash}}">
                            </div>
                            <div class="form-group col-sm-12 col-md-3">
                                <label for="endpoint">Endpoint</label>
                                <input class="form-control bg-white" id="endpoint" type="text" readonly value="https://{{$hostname}}/push">
                            </div>
                            <div class="form-group col-sm-12 col-md-2">
                                <label for="endpoint">Método</label>
                                <input class="form-control bg-white" id="endpoint" type="text" readonly value="POST">
                            </div>
                            <div class="form-group col-sm-12 col-md-8">
                                <span>Instruções para uso de API:</span>
                                <ul class="mb-0">
                                    <li><code>private_key</code> - chave privada gerada acima.</li>
                                    <li><code>subject</code> - assunto da mensagem (max. 512 caracteres).</li>
                                    <li><code>message</code> - corpo da mensagem (max. 2048 caracteres).</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-header">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#block2" aria-expanded="true" aria-controls="block">
                            Últimas mensagens recebidas no canal
                        </button>
                    </div>
                    <div class="card-body collapse show" id="block2">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th scope="col" width="5%"><small><b>ID</b></small></th>
                                <th scope="col" width="20%"><small><b>Data e hora</b></small></th>
                                <th scope="col" width="25%"><small><b>Assunto</b></small></th>
                                <th scope="col" width="50%"><small><b>Mensagem</b></small></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($messages as $message)
                                <tr>
                                    <td scope="col" width="5%"><small>{{$message->id}}<small></td>
                                    <td scope="col" width="20%"><small>{{$message->created_at->toString()}}<small></td>
                                    <td scope="col" width="25%" style="word-wrap: break-word;"><small>{{$message->subject}}<small></td>
                                    <td scope="col" width="50%" style="word-wrap: break-word;"><small>{{$message->message}}<small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan=4 class="text-center">Não há mensagens recebidas neste canal.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        {{ $messages->render() }}
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#block3" aria-expanded="true" aria-controls="block3">
                            Chaves públicas
                        </button>
                    </div>
                    <div class="card-body collapse show" id="block3">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th scope="col" width="20%"><small><b>Data e hora</b></small></th>
                                <th scope="col" width="50%"><small><b>Chave pública</b></small></th>
                                <th scope="col" width="25%" style="word-wrap: break-word;"><small><b>Texto</b></small></th>
                                <th scope="col" width="5%"><small></small></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($public_keys as $key)
                                <tr>
                                    <td scope="col" width="20%"><small>{{$key->created_at->toString()}}<small></td>
                                    <td scope="col" width="50%"><code>{{$key->public_key}}<code></td>
                                    <td scope="col" width="25%" style="word-wrap: break-word;"><small>{{$key->text}}<small></td>
                                    <th scope="col" width="5%"><a class="btn btn-sm btn-link" href="{{route('EditPublicKeyView', ['public_key' => $key->public_key, 'hash' => $hash])}}">Editar</a></th>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan=4 class="text-center">Não há chaves públicas criadas neste canal.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex w-100">
                            <a href="{{route('CreatePublicKey', ['hash' => $hash])}}" class="btn btn-link ml-auto">
                                Criar chave pública
                            </a>
                        </div>
                        {{ $public_keys->render() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">

        </div>
    </div>
</div>
