@extends('Backend::layouts.auth')

@section('title')
    Login
@stop

@section('content')
<section class="material-half-bg">
    <div class="cover"></div>
</section>

<section class="login-content">
    <div class="logo">
        <h1>x.d.m</h1>
    </div>
    @if(session()->has('message-error'))
        <span class="badge badge-danger">
            <strong>Oh snap!</strong> {{ session()->get('message-error') }}
        </span>
    @endif
    <div class="login-box">
    {!! Form::open(['url' => route('backend-post-login'), 'class' => 'login-form', 'name' => 'loginForm', 'id' => 'loginForm', 'role' => 'form']) !!}
        <h3 class="login-head"><i class="fa fa-lg fa-fw fa-user"></i>SIGN IN</h3>
        <div class="form-group has-feedback">
            <label class="control-label">Email</label>
            {!! Form::email('email', '', ['class' => 'form-control input-sm', 'required'=>true, 'autofocus' => true, 'placeholder' => 'Email']) !!}
            
        </div>

        <div class="form-group has-feedback">
            <label class="control-label">Password</label>
            {!! Form::password('password', ['id'=>'password', 'class' => 'form-control input-sm', 'required'=>true, 'placeholder' => 'Password']) !!}
        </div>

        <div class="form-group">
            <div class="utility">
                <div class="animated-checkbox">
                    <label>
                        <input type="checkbox" name="remember_me" value="1"><span class="label-text">Stay Signed in</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group btn-container">
            <button class="btn btn-primary btn-block"><i class="fa fa-sign-in fa-lg fa-fw"></i>Sign In</button>
        </div>
    {!! Form::close() !!}
    </div>
</section>
@stop

@section('scripts')
<script type="text/javascript">
    // Login Page Flipbox control
    $('.login-content [data-toggle="flip"]').click(function() {
        $('.login-box').toggleClass('flipped');
        return false;
    });
  </script>
@stop