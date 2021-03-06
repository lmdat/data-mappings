@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i> Import Ledger Data</h1>
        <p>Import</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'importForm', 'id' => 'importForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Step 2/3 <small class="text-muted">Mapping Column to Data Field</small>
            </h4>    
            <div class="tile-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-md-5">
                                <label class="control-label">Fields</label>                       
                            </div>
        
                            <div class="col-md-6">
                                <label class="control-label">Column Headers</label>                       
                            </div>
                        </div>
                        @foreach($ledger_fields as $k => $v)
                        <div class="form-group row">
                            <div class="col-md-5">
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
                            <div class="col-md-5">
                                <label class="control-label">Dimension Type</label>                       
                            </div>
        
                            <div class="col-md-6">
                                <label class="control-label">Column Headers</label>                       
                            </div>
                        </div>
                        @foreach($dim_types as $v)
                        <div class="form-group row">
                            <div class="col-md-5">
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
            {!! Form::hidden('step', $step) !!}
            {!! Form::hidden('skip_headers', $skip_headers) !!}            
        {!! Form::close() !!}
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    $(function(){
        $("#importForm").on('submit', function(e){

            var n = $("select[name='ledger_header[]']").length;
            var k = 0;
            $("select[name='ledger_header[]']").each(function(){
                if($(this).val() != "0"){
                    k++;
                }
            });

            if(n == k){
               $(this).submit(); 
            }
            else{
                e.preventDefault();
                if(k == 0){
                    alert('Please select column for each field.');
                }                    
                else{
                    alert('Please select ' + (n - k) + ' more column for each field.');
                }
            }
        });
    });
</script>
@stop