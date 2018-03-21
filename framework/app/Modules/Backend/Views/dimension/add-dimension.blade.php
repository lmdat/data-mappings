@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i> Define Dimension</h1>
        <p>Import data of Dimension</p>
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        @include('Backend::dimension.list-dimension', ['entries' => $entries, 'qs' => $qs])
    </div>

    <div class="col-md-5">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'dimForm', 'id' => 'dimForm', 'role' => 'form', 'files' => true]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Insert Dimension
                {{--  @if(session()->has('error-message'))
                    <small><label class="badge badge-danger">Oh snap! {{ session()->get('error-message') }}</label></small>
                @endif

                @if(session()->has('success-message'))
                    <small><label class="badge badge-success">Yeah! {{ session()->get('success-message') }}</label></small>
                @endif  --}}
            </h4>
            <div class="tile-body">
                <div class="form-group">
                    <label class="control-label">Company</label>
                    {!! Form::select('company_id', $companies, '', ['class' => 'form-control', 'id' => 'company_id']) !!}
                    
                </div>

                <div class="form-group">
                    <label class="control-label">Dimension Type</label>
                    {!! Form::select('dim_type', $type_list, '', ['class' => 'form-control', 'id' => 'dim_type']) !!}
                    
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">Dimension Code</label>
                        {!! Form::text('dim_code', '', ['id'=>'dim_code', 'class' => 'form-control']) !!}
                   </div>

                   <div class="col-md-6">
                         <label class="control-label">Dimension Name</label>
                         {!! Form::text('dim_name', '', ['id'=>'dim_name', 'class' => 'form-control']) !!}
                   </div>
                </div>

                <div class="animated-checkbox">
                    <label>
                        {!! Form::checkbox('show_multiple', '1', false, ['id' => 'chk_show_multiple']) !!}<span class="label-text">Insert multiple dimensions?</span>
                    </label>
                </div>

                <div id='file_upload' class="container">
                    <div class="form-group">
                        <label class="control-label">Data File(csv)</label>
                        {!! Form::file('data_file', ['id'=>'data_file', 'class' => 'form-control']) !!}
                        <small class="form-text text-muted">The order of colunms: <strong>DIMENSION-CODE;DIMENSION-NAME</strong></small>
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
        $('#file_upload').hide()
        $('#chk_show_multiple').on('click', function(){
            if($(this).is(':checked')){
                $('#dim_code').attr('disabled', true);
                $('#dim_name').attr('disabled', true);
                $('#file_upload').show(500);
            }
            else{
                $('#dim_code').attr('disabled', false);
                $('#dim_name').attr('disabled', false);
                $('#file_upload').hide(500);
            }
                
        });
    })
</script>
@stop