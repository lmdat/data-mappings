@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i> Manage User</h1>
        <p>Create/Edit User</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        @include('Backend::user.list-user', ['entries' => $entries, 'qs' => $qs])
    </div>

    <div class="col-md-4">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'userForm', 'id' => 'userForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Create User
                {{--  @if(session()->has('error-message'))
                    <small><label class="badge badge-danger">Oh snap! {{ session()->get('error-message') }}</label></small>
                @endif

                @if(session()->has('success-message'))
                    <small><label class="badge badge-success">Yeah! {{ session()->get('success-message') }}</label></small>
                @endif  --}}
            </h4>
            <div class="tile-body">
               
                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">First Name</label>
                        {!! Form::text('first_name', '', ['id'=>'first_name', 'class' => 'form-control']) !!}
                   </div>

                   <div class="col-md-6">
                         <label class="control-label">Last Name</label>
                         {!! Form::text('last_name', '', ['id'=>'last_name', 'class' => 'form-control']) !!}
                   </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">Email</label>
                        {!! Form::email('email', '', ['id'=>'email', 'class' => 'form-control']) !!}
                    </div>

                    <div class="col-md-6">
                        <label class="control-label">Password</label>
                        {!! Form::password('password', ['id'=>'password', 'class' => 'form-control']) !!}
                        
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Role</label>
                    <select id="role_id" name="role_id" class="form-control">
                        <option value="" selected>---</option>
                        @foreach($roles as $role)
                        <option value="{{$role->id}}" @if(in_array($role->power, $disabled_roles)) disabled @endif>{{ $role->role_name }}</option>
                        @endforeach
                    </select>
                    
                    {{--  {!! Form::select('role_id', $roles, '', ['class' => 'form-control', 'id' => 'company_id']) !!}  --}}
                    
                </div>

                {{--  <div class="animated-checkbox">
                    <label>
                        {!! Form::checkbox('show_multiple', '1', false, ['id' => 'chk_show_multiple']) !!}<span class="label-text">Insert multiple accounts?</span>
                    </label>
                </div>

                <div id='file_upload' class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Data File(csv)</label>
                        {!! Form::file('data_file', ['id'=>'data_file', 'class' => 'form-control']) !!}
                        <small class="form-text text-muted">The order of colunms: <strong>ACCOUNT-CODE;ACCOUNT-NAME</strong></small>
                    </div>

                    <div class="animated-checkbox">
                        <label>
                            {!! Form::checkbox('skip_first_line', '1', false, ['id' => 'chk_skip']) !!}<span class="label-text">Skip first line for headers?</span>
                        </label>
                    </div>
                </div>  --}}

                {{--  <div class="form-group" id="multiple_account_container">
                    <small class="form-text text-muted">Use the fotmat: ACCOUNT_CODE|ACCOUNT_NAME</small>
                    {!! Form::textarea('multiple_account', '', ['id'=>'multiple_account', 'class' => 'form-control', 'placeholder' => '122030|Telephone']) !!}
                </div>  --}}
            </div>
            <div class="tile-footer text-right">
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>Save</button>
            </div>
            
        </div>
        {!! Form::close() !!}
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    
</script>
@stop