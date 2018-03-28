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
    <div class="col-md-8">
        @include('Backend::dimension.list-dimension', ['entries' => $entries, 'qs' => $qs, 'curr_id' => $dim->id])
    </div>

    <div class="col-md-4">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'put', 'name' => 'dimForm', 'id' => 'dimForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Edit Account
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
                    {!! Form::select('company_id', $companies, $dim->company_id, ['class' => 'form-control', 'id' => 'company_id']) !!}
                    
                </div>  --}}

                <div class="form-group">
                    <label class="control-label">Dimension Type <span class="text-danger">*</span></label>
                    {!! Form::select('dim_type', $type_list, old('dim_type', $dim->dim_type), ['class' => 'form-control', 'id' => 'dim_type']) !!}
                    @if ($errors->has('dim_type'))<p class="text-danger"><small>{!!$errors->first('dim_type')!!}</small></p> @endif 
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">Dimension Code</label>
                        {!! Form::text('dim_code', $dim->dim_code, ['id'=>'dim_code', 'class' => 'form-control', 'readonly' => true]) !!}
                   </div>

                   <div class="col-md-6">
                         <label class="control-label">Dimension Name <span class="text-danger">*</span></label>
                         {!! Form::text('dim_name', old('dim_name', $dim->dim_name), ['id'=>'dim_name', 'class' => 'form-control']) !!}
                         @if ($errors->has('dim_name'))<p class="text-danger"><small>{!!$errors->first('dim_name')!!}</small></p> @endif 
                   </div>
                </div>

                {{--  <div class="animated-checkbox">
                    <label>
                        {!! Form::checkbox('show_multiple', '1', false, ['id' => 'chk_show_multiple']) !!}<span class="label-text">Insert multiple items?</span>
                    </label>
                </div>

                <div class="form-group" id="multiple_item_container">
                    <small class="form-text text-muted">Use the fotmat: ITEM_NAME|SHORT_NAME or just ITEM_NAME</small>
                    {!! Form::textarea('multiple_item', '', ['id'=>'multiple_item', 'class' => 'form-control', 'placeholder' => 'ITEM_NAME|SHORT_NAME or just ITEM_NAME']) !!}
                </div>  --}}
            </div>
            <div class="tile-footer text-right">
                <a href="{{ route('dimension', [str_replace('?', '', $qs)]) }}" class="btn btn-danger" role="button"><i class="fa fa-reply"></i>Back</a>
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>Save</button>
            </div>
            
        </div>
            {!! Form::hidden('id', $dim->id) !!}    
        {!! Form::close() !!}
    </div>
</div>
@stop

@section('scripts')

@stop