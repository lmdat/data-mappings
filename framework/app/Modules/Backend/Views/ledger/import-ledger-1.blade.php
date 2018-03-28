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
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'importForm', 'id' => 'importForm', 'role' => 'form', 'files' => true]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Step 1/3 <small class="text-muted">Select the excel/csv file</small>
            </h4>    
            <div class="tile-body">
                
                <div class="form-group">
                    <label class="control-label">Data File(xls, xlsx, csv)</label>
                    {!! Form::file('data_file', ['id'=>'data_file', 'accept'=>'.csv, .xls, .xlsx', 'class' => 'form-control']) !!}
                    @if ($errors->has('data_file'))<p class="text-danger"><small>{!!$errors->first('data_file')!!}</small></p> @endif  
                </div>  
                <div class="animated-checkbox">
                    <label>
                        {!! Form::checkbox('skip_first_line', '1', false, ['id' => 'chk_skip']) !!}<span class="label-text">Skip first line for headers?</span>
                    </label>
                </div>   

                <hr>
                <div class="animated-radio-button">
                    <label>
                        {!! Form::radio('upload_type', 1, true, ['id' => 'rdo_upload_new']) !!}<span class="label-text">New Import</span>
                    </label>
                    @if($total_upload > 0)
                    <label style="margin-left: 10px">
                        {!! Form::radio('upload_type', 2, false, ['id' => 'rdo_upload_revision', 'style'=>'display: none']) !!}<span class="label-text">Revision Import</span>
                    </label>
                    @endif
                </div>

                
                <div class="col-md-6">
                    <div id="new_import" class="form-group">
                        <label class="control-label">Upload Title</label>
                        {!! Form::text('upload_title', '', ['id'=>'upload_title', 'class' => 'form-control']) !!}
                        @if ($errors->has('upload_title'))<p class="text-danger"><small>{!!$errors->first('upload_title')!!}</small></p> @endif  
                    </div>     
                    @if($total_upload > 0)
                    <div id="revision_import" class="form-group">
                        <label class="control-label">Select Upload Title</label>
                        {!! Form::select('upload_id', $upload_ledgers, '', ['class' => 'form-control', 'id' => 'upload_id']) !!}
                    </div>  
                    @endif    
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

        //$('#rdo_upload_new').attr('checked', true);

        @if($total_upload > 0)
        $('#revision_import').hide();
        @endif 
        
        $('input[type=radio]').on('click', function(){
            if($(this).val() == 1){
                $('#new_import').show(300);
                $('#revision_import').hide(300);
            }
            else if($(this).val() == 2){
                $('#new_import').hide(300);
                $('#revision_import').show(300);
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