@extends('layouts.app')

@section('content')

<div class="container h-100">
    <div class="row justify-content-center">
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <a href="{{route('home')}}">Página inicial</a> / Informações do canal de mensagems
                </div>
                <div class="card-body">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-12 mt-1">
            <div class="card">
                <div class="card-header">
                    Últimas mensagens recebidas
                </div>
                <div class="card-body">
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
                                <td colspan=4 class="text-center">Não há mensagens neste canal.</td>
                            </tr>
                          @endforelse
                        </tbody>
                    </table>
                    {{ $messages->render() }}
                </div>
            </div>
        </div>
    </div>
</div>
