@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i> Manage Company</h1>
        <p>Create/Edit/Select Company</p>
    </div>
</div>

<div class="row justify-content-center">
   
    <div class="col-md-6">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'comForm', 'id' => 'comForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Select Company
            </h4>
            <div class="tile-body">
                @if($selected_company == null)
                <p class="p-3 mb-2 bg-warning text-dark">No company selected. Please select one before start to work.</p>
                @else
                <p class="p-3 mb-2 bg-info text-white">You are working with: {{$selected_company->company_name}} </p>
                @endif
                <div class="form-group">
                    <label class="control-label">Company will be handle:</label>
                    {!! Form::select('company_id', $companies, $selected_company, ['class' => 'form-control', 'id' => 'company_id']) !!}
                    
                </div>

            </div>
            <div class="tile-footer text-right">
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>Select</button>
            </div>
            
        </div>
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