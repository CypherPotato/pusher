@extends('layouts.app')

@section('content')

<div class="container h-100">
    <div class="row justify-content-center">
        <div class="col-12 mt-3">
            <div id="accordion">
                <div class="card mb-2">
                    <div class="card-header">
                        Criação de chave pública
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
                        </div>
                        <form action="{{route('createKeyPair')}}">
                            <input hidden type="text" name="hash" value="{{$hash}}">
                            <input hidden type="text" name="public_key" value="{{$public_key ?? "-1"}}">
                            <div class="form-group">
                                <label for="exampleFormControlFile1">Mensagem da chave pública</label>
                                <textarea class="form-control" style="min-height: 256px" maxlength="2048" name="message">{{$text ?? ""}}</textarea>
                            </div>
                            @if(\Session::has("message"))
                                <span>
                                    {{\Session::get("message")}}
                                </span>
                            @endif
                            <div class="form-group d-flex">
                                <div class="ml-auto">
                                    @if(isset($public_key))
                                    <a href="{{route('DeleteKeyPair', ['hash' => $hash, 'public_key' => $public_key])}}" class="btn btn-outline-danger mr-2">Excluir chave pública</a>
                                    @endif
                                    <button type="submit" class="btn btn-primary">Criar chave pública</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
