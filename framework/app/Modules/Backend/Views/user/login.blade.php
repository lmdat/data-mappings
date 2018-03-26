@extends('Backend::layouts.auth')

@section('title')
    "Login"
@stop

@section('content')
<section class="material-half-bg">
    <div class="cover"></div>
</section>

<section class="login-content">
    <div class="logo">
        <h1>Vali</h1>
    </div>
    <div class="login-box">
    @if(!Session::has('message-error'))
        <div class="login-box-msg">{{ trans($lang_mod . '.title') }}</div>
    @else
        <div class="alert alert-danger"><strong>Oh snap!</strong> {{ Session::get('message-error') }}</div>
    @endif

    {!! Form::open(['url' => Request::url(), 'name' => 'loginForm', 'id' => 'loginForm', 'role' => 'form']) !!}
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
   
</script>
@stop