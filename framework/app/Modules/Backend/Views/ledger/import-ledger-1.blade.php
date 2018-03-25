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
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'importForm', 'id' => 'importForm', 'role' => 'form', 'files' => true]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Step 1/3 <small class="text-muted">Select the excel/csv file</small>
            </h4>    
            <div class="tile-body">
                
                <div class="form-group">
                    <label class="control-label">Data File(xlsx, csv)</label>
                    {!! Form::file('data_file', ['id'=>'data_file', 'class' => 'form-control']) !!}
                </div>  
                <div class="animated-checkbox">
                    <label>
                        {!! Form::checkbox('skip_first_line', '1', false, ['id' => 'chk_skip']) !!}<span class="label-text">Skip first line for headers?</span>
                    </label>
                </div>   

                <hr>
                <div class="animated-radio-button">
                    <label>
                        {!! Form::radio('upload_type', 1, true, ['id' => 'chk_upload1']) !!}<span class="label-text">New Import</span>
                    </label>
                    <label style="margin-left: 10px">
                        {!! Form::radio('upload_type', 2, false, ['id' => 'chk_upload2']) !!}<span class="label-text">Revision Import</span>
                    </label>
                </div>
                <div class="container">
                    <div id="new_import" class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Upload Title</label>
                            {!! Form::text('upload_title', '', ['id'=>'upload_title', 'class' => 'form-control']) !!}
                        </div>      
                    </div>
                    
                    <div id="revision_import" class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Select Upload Title</label>
                            {!! Form::select('upload_id', $upload_ledgers, '', ['class' => 'form-control', 'id' => 'upload_id']) !!}
                        </div>      
                    </div>
                </div>
                      
            </div>
            <div class="tile-footer text-right">
                    <button class="btn btn-primary" type="submit">Next <i class="fa fa-angle-double-right"></i></button>
            </div>
        </div>
            {!! Form::hidden('step', $step) !!} 
        {!! Form::close() !!}
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    $(function(){
        $('#revision_import').hide();

        $('input[type=radio]').on('click', function(){
            if($(this).val() == 1){
                $('#new_import').show(500);
                $('#revision_import').hide(500);
            }
            else if($(this).val() == 2){
                $('#new_import').hide(500);
                $('#revision_import').show(500);
            }
            
        });

        $('#data_file').on('change', function(e){
            
            // if($('input[type=radio][value=1]').is(':checked')){
            //     $('#upload_title').val(e.target.files[0].name);
            // }
            $('#upload_title').val(e.target.files[0].name);
        })
    })
</script>
@stop