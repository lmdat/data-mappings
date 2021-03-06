@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i> Create/Edit Account</h1>
        <p>Create Account</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        @include('Backend::account.list-account', ['entries' => $entries, 'qs' => $qs])
    </div>

    <div class="col-md-4">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'accountForm', 'id' => 'accountForm', 'role' => 'form', 'files' => true]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Insert Account
                {{--  @if(session()->has('error-message'))
                    <small><label class="badge badge-danger">Oh snap! {{ session()->get('error-message') }}</label></small>
                @endif

                @if(session()->has('success-message'))
                    <small><label class="badge badge-success">Yeah! {{ session()->get('success-message') }}</label></small>
                @endif  --}}
            </h4>
            <div class="tile-body">
                {{--  <div class="form-group">
                    <label class="control-label">Company</label>
                    {!! Form::select('company_id', $companies, '', ['class' => 'form-control', 'id' => 'company_id']) !!}
                    
                </div>  --}}

                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">Account Code <span class="text-danger">*</span></label>
                        {!! Form::text('account_code', '', ['id'=>'account_code', 'class' => 'form-control', 'autofocus', 'required']) !!}
                        @if ($errors->has('account_code'))<p class="text-danger"><small>{!!$errors->first('account_code')!!}</small></p> @endif
                   </div>

                   <div class="col-md-6">
                         <label class="control-label">Account Name <span class="text-danger">*</span></label>
                         {!! Form::text('account_name', '', ['id'=>'account_name', 'class' => 'form-control', 'required']) !!}
                         @if ($errors->has('account_name'))<p class="text-danger"><small>{!!$errors->first('account_name')!!}</small></p> @endif
                   </div>
                </div>

                <div class="animated-checkbox">
                    <label>
                        {!! Form::checkbox('show_multiple', '1', false, ['id' => 'chk_show_multiple']) !!}<span class="label-text">Insert multiple accounts?</span>
                    </label>
                </div>

                <div id='file_upload' class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Data File(xls, xlsx, csv)</label>
                        {!! Form::file('data_file', ['id'=>'data_file', 'accept'=>'.csv, .xls, .xlsx', 'class' => 'form-control']) !!}
                        <small class="form-text text-muted">The order of colunms: <strong>ACCOUNT-CODE | ACCOUNT-NAME</strong></small>
                        @if ($errors->has('data_file'))<p class="text-danger"><small>{!!$errors->first('data_file')!!}</small></p> @endif
                    </div>

                    <div class="animated-checkbox">
                        <label>
                            {!! Form::checkbox('skip_first_line', '1', false, ['id' => 'chk_skip']) !!}<span class="label-text">Skip first line for headers?</span>
                        </label>
                    </div>
                </div>

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
    $(function(){
        
        if($('#chk_show_multiple').is(':checked')){
            $('#file_upload').show();
            $('#account_code').attr('disabled', true);
            $('#account_name').attr('disabled', true);
        }
        else{
            $('#account_code').attr('disabled', false);
            $('#account_name').attr('disabled', false);
            $('#file_upload').hide();
        }
        $('#chk_show_multiple').on('click', function(){
            if($(this).is(':checked')){
                $('#account_code').attr('disabled', true);
                $('#account_name').attr('disabled', true);
                $('#file_upload').show(500);
            }
            else{
                $('#account_code').attr('disabled', false);
                $('#account_name').attr('disabled', false);
                $('#file_upload').hide(500);
            }
                
        });
    })
</script>
@stop