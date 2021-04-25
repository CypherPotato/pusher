@extends('layouts.app')

@section('content')

<div class="container h-100">
    <div class="row h-100 justify-content-center">
        <div class="col-md-8 my-auto">
            <div class="card d-flex w-100 p-4 border-0 shadow">
                <img src="{{asset('img/pusher.png')}}" class="mx-auto mb-3" style="height: auto; width: 70%">
                <form id="key-form" method="GET" action="{{route('ViewMessages')}}">
                    <div class="form-group">
                      <label for="exampleInputEmail1">Your access</label>
                      <input type="text" class="form-control" name="id">
                      <small id="emailHelp" class="form-text text-muted">If you don't have an access, we will create one for you.</small>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputPassword1">Private key</label>
                      <input type="text" class="form-control" name="privateKey" placeholder="It would be an password" maxlength="64">
                    </div>
                    @if(\Session::has("message"))
                    <div class="form-group">
                        <span class="text-danger">{{\Session::get("message")}}</span>
                    </div>
                    @endif
                    <button type="submit" class="btn text-center btn-primary pusher-bg g-recaptcha"
                        {!! env("AP_DEBUG") == true ? '' : 'data-sitekey="' . env('RECAPTCHA_WEBSITE_KEY') . '" data-callback="onSubmit" data-action="submit"' !!}
                    >Access the dashboard</button>
                </form>
            </div>
            <p class="text-center mt-4">
                Pusher is an open-source project available at <a href="https://github.com/CypherPotato/pusher">~/CypherPotato/pusher</a>
            </p>
        </div>
    </div>
</div>

<script>
    function onSubmit(token) {
        document.getElementById("key-form").submit();
    }
</script>