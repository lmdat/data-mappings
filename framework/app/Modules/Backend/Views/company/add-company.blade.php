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

<div class="row">
    <div class="col-md-8">
        @include('Backend::company.list-company', ['entries' => $entries, 'qs' => $qs])
    </div>

    <div class="col-md-4">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'comForm', 'id' => 'comForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Create Company
                {{--  @if(session()->has('error-message'))
                    <small><label class="badge badge-danger">Oh snap! {{ session()->get('error-message') }}</label></small>
                @endif

                @if(session()->has('success-message'))
                    <small><label class="badge badge-success">Yeah! {{ session()->get('success-message') }}</label></small>
                @endif  --}}
            </h4>
            <div class="tile-body">
               
                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">Company Name <span class="text-danger">*</span></label>
                        {!! Form::text('company_name', '', ['id'=>'company_name', 'class' => 'form-control', 'autofocus', 'required']) !!}
                        @if ($errors->has('company_name'))<p class="text-danger"><small>{!!$errors->first('company_name')!!}</small></p> @endif  
                   </div>

                   <div class="col-md-6">
                         <label class="control-label">Short Name <span class="text-danger">*</span></label>
                         {!! Form::text('short_name', '', ['id'=>'short_name', 'class' => 'form-control', 'required']) !!}
                         @if ($errors->has('short_name'))<p class="text-danger"><small>{!!$errors->first('short_name')!!}</small></p> @endif  
                   </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">Phone</label>
                        {!! Form::text('phone', '', ['id'=>'phone', 'class' => 'form-control']) !!}
                    </div>

                    <div class="col-md-6">
                            <label class="control-label">Mobile</label>
                            {!! Form::text('mobile', '', ['id'=>'mobile', 'class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Be handle by</label>
                    {!! Form::select('user_id[]', $users, '', ['class' => 'form-control', 'id' => 'user_id', 'multiple']) !!}
                    
                </div>

                
            </div>
            <div class="tile-footer text-right">
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>Save</button>
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
        $('#user_id').select2({
            width: '100%'
        });
    })
</script>
@stop