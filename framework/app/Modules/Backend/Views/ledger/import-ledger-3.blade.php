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
                Step 3/3 <small class="text-muted">Finish</small>

                @if(session()->has('error-message'))
                    <label class="badge badge-danger">Oh snap! {{ session()->get('error-message') }}</label>
                @endif

                @if(session()->has('success-message'))
                    <label class="badge badge-success">Yeah! {{ session()->get('success-message') }}</label>
                @endif 
            </h4>    
            <div class="tile-body">
                @if($result != null)
               
                <p class="h5"><span class="text-primary">{{ $result['ledger'] }}</span> ledger keys are inserted.</p>
                <p class="h5"><span class="text-primary">{{ $result['account'] }}</span> new accounts are detected and inserted.</p>
                <p class="h5"><span class="text-primary">{{ $result['dim'] }}</span> new dimentions are detected and inserted.</p>
                @endif
            </div>
            <div class="tile-footer text-center">
                @if($result != null)
                    <button class="btn btn-primary" type="submit">Go back Ledger List</button>
                @else
                    <button class="btn btn-warning" type="submit">Try Again! <i class="fa fa-angle-double-right"></i></button>
                    {!! Form::hidden('try_again', 1) !!} 
                @endif

                   
                
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