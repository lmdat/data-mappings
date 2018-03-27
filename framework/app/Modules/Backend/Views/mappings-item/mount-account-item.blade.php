@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i>Mount Ledger Key to Item</h1>
        <p>Glue Item Code with Ledger Key</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'itemForm', 'id' => 'itemForm', 'role' => 'form', 'files' => true]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Quick Import
                @if(session()->has('error-message'))
                    <small><label class="badge badge-danger">Oh snap! {{ session()->get('error-message') }}</label></small>
                @endif

                @if(session()->has('success-message'))
                    <small><label class="badge badge-success">Yeah! {{ session()->get('success-message') }}</label></small>
                @endif
            </h4>
            <div class="tile-body">
                <div class="form-group">
                    <label class="control-label">Data File(xlsx, csv)</label>
                    {!! Form::file('data_file', ['id'=>'data_file', 'class' => 'form-control']) !!}
                    <small class="form-text text-muted">Order of Column: MAPPING_ITEM_ID | LEDGER_KEY</small>
                </div>  
                <div class="animated-checkbox">
                    <label>
                        {!! Form::checkbox('skip_headers', '1', false, ['id' => 'chk_skip']) !!}<span class="label-text">Skip first line for headers?</span>
                    </label>
                </div>   
                
                
                
            </div>
            <div class="tile-footer text-right">
                    <a href="{{ route('mappings-item', [str_replace('?', '', $qs)]) }}" class="btn btn-danger" role="button"><i class="fa fa-reply"></i>Back</a>
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>Save</button>
            </div>
            
        </div>
        
        {!! Form::close() !!}
    </div>
</div>
@stop

@section('css-link')
{{--  <link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>  --}}
@stop

@section('js-link')
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
@stop


@section('scripts')
<script type="text/javascript">
    $(function(){
        // $('#mounted_account').select2({
        //     width: '100%'
        // });
    })
</script>
@stop