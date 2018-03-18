@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i> Define Mappings Item</h1>
        <p>Import data of dimension from excel or csv file</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="tile">
            <h4 class="tile-title">Dimension List</h4>
            <div class="tile-body">
                    <table class="table table-hover table-bordered" id="item_table">
                        
                    </table>
            </div>
            {{--  <div class="tile-footer"><a class="btn btn-primary" href="#">Link</a></div>  --}}
        </div>
    </div>

    <div class="col-md-6">
        {!! Form::open(['url' => Request::url() . $qs, 'name' => 'itemForm', 'id' => 'itemForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">Insert Dimension</h4>
            <div class="tile-body">
                <div class="form-group">
                    <label class="control-label">Dimension Type</label>
                    {!! Form::select('type_id', $type_list, '', ['class' => 'form-control', 'id' => 'type_id']) !!}
                    
                </div>

                <div class="form-group">
                        <label class="control-label">Data File(xls, xlsx, csv)</label>
                    {!! Form::file('data_input', ['id'=>'data_input', 'class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <label class="form-check-label">
                        <input class="form-check-input" type="checkbox">Skip first line for headers?
                        </label>
                    </div>
                </div>
            </div>
            <div class="tile-footer text-right">
                <button class="btn btn-primary" type="button"><i class="fa fa-upload"></i>Upload</button>
            </div>
            
        </div>
        {!! Form::close() !!}
    </div>
</div>
@stop