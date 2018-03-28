@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i> Settings</h1>
        <p>Truncate data table</p>
    </div>
</div>

<div class="row justify-content-center">
   
    <div class="col-md-6">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'settingForm', 'id' => 'settingForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Truncate
                @if(session()->has('error-message'))
                    <small><label class="badge badge-danger">Oh snap! {{ session()->get('error-message') }}</label></small>
                @endif

                @if(session()->has('success-message'))
                    <small><label class="badge badge-success">Well done! {{ session()->get('success-message') }}</label></small>
                @endif

                @if(session()->has('warning-message'))
                    <small><label class="badge badge-warning">Warning! {{ session()->get('warning-message') }}</label></small>
                @endif
            </h4>
            <div class="tile-body">
                @foreach($table_names as $table)
                <div class="animated-checkbox">
                    <label>
                    {!! Form::checkbox('table[]', $table, false, ['id' => 'chk_table' . $loop->iteration]) !!}<span class="label-text">Table {{$table}}</span>
                    </label>
                </div>
                @endforeach
            </div>
            <div class="tile-footer text-right">
                <button class="btn btn-primary" type="submit"><i class="fa fa-times"></i>Truncate</button>
            </div>
            
        </div>
        {!! Form::close() !!}
    </div>
</div>
@stop