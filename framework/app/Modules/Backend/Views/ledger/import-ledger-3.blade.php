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
                <p class="h5"><span class="text-primary">{{ $result['dim'] }}</span> new dimensions are detected and inserted.</p>
                @endif
            </div>
            <div class="tile-footer text-center">
                @if($result != null)
                    <a href="{{ route('ledger', [str_replace('?', '', $qs)]) }}" class="btn btn-primary" role="button">Go back Ledger List</a>
                    <a href="{{ route('import-ledger', ['step' => 1, str_replace('?', '', $qs)]) }}" class="btn btn-info" role="button">Continue Import</a>
                @else
                    <a href="{{ route('import-ledger', ['step' => 1, str_replace('?', '', $qs)]) }}" class="btn btn-warning" role="button">Try Again!</a>
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
    
</script>
@stop