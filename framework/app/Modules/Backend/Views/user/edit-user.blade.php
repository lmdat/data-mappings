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
        @include('Backend::user.list-user', ['entries' => $entries, 'qs' => $qs, 'curr_id' => $user->id])
    </div>

    <div class="col-md-4">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'put', 'name' => 'userForm', 'id' => 'userForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Edit User
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
                        <label class="control-label">First Name <span class="text-danger">*</span></label>
                        {!! Form::text('first_name', $user->first_name, ['id'=>'first_name', 'class' => 'form-control', 'required']) !!}
                        @if ($errors->has('first_name'))<p class="text-danger"><small>{!!$errors->first('first_name')!!}</small></p> @endif
                   </div>

                   <div class="col-md-6">
                         <label class="control-label">Last Name <span class="text-danger">*</span></label>
                         {!! Form::text('last_name', $user->last_name, ['id'=>'last_name', 'class' => 'form-control', 'required']) !!}
                         @if ($errors->has('last_name'))<p class="text-danger"><small>{!!$errors->first('last_name')!!}</small></p> @endif
                   </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">Email <span class="text-danger">*</span></label>
                        {!! Form::email('email', $user->email, ['id'=>'email', 'class' => 'form-control', 'required']) !!}
                        @if ($errors->has('email'))<p class="text-danger"><small>{!!$errors->first('email')!!}</small></p> @endif
                    </div>

                    <div class="col-md-6">
                        <label class="control-label">Password <small class="text-muted">(Empty for no change)</small></label>
                        {!! Form::password('password', ['id'=>'password', 'class' => 'form-control']) !!}
                        @if ($errors->has('password'))<p class="text-danger"><small>{!!$errors->first('password')!!}</small></p> @endif
                        
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Role <span class="text-danger">*</span></label>
                    <select id="role_id" name="role_id" class="form-control" required>
                        <option value="">---</option>
                        @foreach($roles as $role)
                        <option value="{{$role->id}}"
                            @if(in_array($role->power, $disabled_roles)) disabled @endif
                            @if($role->id == $user->roleId())) selected @endif>
                            {{ $role->role_name }}
                        </option>
                        @endforeach
                    </select>
                    @if ($errors->has('role_id'))<p class="text-danger"><small>{!!$errors->first('role_id')!!}</small></p> @endif
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
                    <a href="{{ route('user', [str_replace('?', '', $qs)]) }}" class="btn btn-danger" role="button"><i class="fa fa-reply"></i>Back</a>
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>Save</button>
            </div>
            
        </div>
            {!! Form::hidden('id', $user->id) !!}
        {!! Form::close() !!}
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    
</script>
@stop