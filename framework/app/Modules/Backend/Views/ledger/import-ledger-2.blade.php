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