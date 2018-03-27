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
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'userForm', 'id' => 'userForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Assign Company
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
                        {!! Form::text('first_name', $user->first_name, ['id'=>'first_name', 'class' => 'form-control', 'readonly']) !!}
                   </div>

                   <div class="col-md-6">
                         <label class="control-label">Last Name</label>
                         {!! Form::text('last_name', $user->last_name, ['id'=>'last_name', 'class' => 'form-control', 'readonly']) !!}
                   </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Company</label>
                    <?php
                        $arr_selected = [];
                        $com_list = $user->companies()->orderBy('company_id', 'DESC')->get();
                        foreach($com_list as $com){
                            $arr_selected[] = $com->id;
                        }

                        // dd($arr_selected);
                    ?>
                    {!! Form::select('company_id[]', $companies, $arr_selected, ['class' => 'form-control', 'id' => 'company_id', "multiple"=>true]) !!}
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

@section('js-link')
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
@stop


@section('scripts')
<script type="text/javascript">
    $(function(){
        $('#company_id').select2({
            width: '100%'
        });
    })
</script>
@stop