@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i>Import Ledger Data</h1>
        <p>Import</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'importForm', 'id' => 'importForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Step 2/2 <small class="text-muted">Mapping Column to Data Field</small>
            </h4>    
            <div class="tile-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label class="control-label">Fields</label>                       
                            </div>
        
                            <div class="col-md-6">
                                <label class="control-label">Column Headers</label>                       
                            </div>
                        </div>
                        @foreach($ledger_fields as $k => $v)
                        <div class="form-group row">
                            <div class="col-md-4">
                                {!! Form::text('field_text' . $loop->index, $v, ['id'=>'field_text', 'class' => 'form-control', 'readonly' => true]) !!}
                                {!! Form::hidden('field_name[]', $k) !!}
                            </div>
        
                            <div class="col-md-6">
                                {!! Form::select('ledger_header[]', $ledger_headers, '', ['class' => 'form-control', 'id' => 'ledger_header']) !!}
                            </div>
                        </div>
                        @endforeach
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label class="control-label">Dimension Type</label>                       
                            </div>
        
                            <div class="col-md-6">
                                <label class="control-label">Column Headers</label>                       
                            </div>
                        </div>
                        @foreach($dim_types as $v)
                        <div class="form-group row">
                            <div class="col-md-4">
                                {!! Form::text('dim_type_text' . $loop->index, $v->type_name, ['id'=>'dim_type_text', 'class' => 'form-control', 'readonly' => true]) !!}
                                {!! Form::hidden('dim_type_id[]', $v->id) !!}
                            </div>
        
                            <div class="col-md-6">
                                {!! Form::select('dim_header[]', $ledger_headers, '', ['class' => 'form-control', 'id' => 'ledger_header']) !!}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="tile-footer text-right">
                <a href="{{ route('import-ledger', ['step' => 1, str_replace('?', '', $qs)]) }}" class="btn btn-danger" role="button"><i class="fa fa-angle-double-left"></i>Previous</a>
                <button class="btn btn-primary" type="submit">Save <i class="fa fa-angle-double-right"></i></button>
            </div>
        </div>
            {!! Form::hidden('step', 2) !!}
        {!! Form::close() !!}
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    $(function(){
        $('#multiple_item_container').hide()
        $('#chk_show_multiple').on('click', function(){
            if($(this).is(':checked')){
                $('#item_name').attr('disabled', true);
                $('#short_name').attr('disabled', true);
                $('#multiple_item_container').show(500);
            }
            else{
                $('#item_name').attr('disabled', false);
                $('#short_name').attr('disabled', false);
                $('#multiple_item_container').hide(500);
            }
                
        });
    })
</script>
@stop