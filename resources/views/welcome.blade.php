@extends('layouts.app')

@section('content')

<div class="container h-100">
    <div class="row h-100 justify-content-center">
        <div class="col-md-8 my-auto">
            <div class="card d-flex w-100 p-4">
                <img src="{{asset('img/pusher.png')}}" class="mx-auto mb-3" style="height: auto; width: 70%">
                <form method="GET" action="{{route('ViewMessages')}}">
                    <div class="form-group">
                      <label for="exampleInputEmail1">Identificação</label>
                      <input type="text" class="form-control" name="id">
                      <small id="emailHelp" class="form-text text-muted">Se sua identificação não existir, criaremos uma para você.</small>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputPassword1">Chave privada</label>
                      <input type="text" class="form-control" name="privateKey" placeholder="Pode ser uma senha" maxlength="64">
                    </div>
                    @if(\Session::has("message"))
                    <div class="form-group">
                        <span class="text-danger">{{\Session::get("message")}}</span>
                    </div>
                    @endif
                    <button type="submit" class="btn text-center btn-primary pusher-bg">Acessar</button>
                </form>
            </div>
        </div>
    </div>
</div>
