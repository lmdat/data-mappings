@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i> Define Account</h1>
        <p>Import data of Account</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        @include('Backend::account.list-account', ['entries' => $entries, 'qs' => $qs, 'curr_id' => $account->id])
    </div>

    <div class="col-md-4">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'put', 'name' => 'accountForm', 'id' => 'accountForm', 'role' => 'form', 'files' => false]) !!}
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
                    {!! Form::select('company_id', $companies, $account->company_id, ['class' => 'form-control', 'id' => 'company_id']) !!}
                    
                </div>  --}}

                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">Account Code</label>
                        {!! Form::text('account_code', $account->account_code, ['id'=>'account_code', 'class' => 'form-control', 'readonly']) !!}
                    </div>

                    <div class="col-md-6">
                        <label class="control-label">Account Name</label>
                        {!! Form::text('account_name', $account->account_name, ['id'=>'account_name', 'class' => 'form-control', 'required']) !!}
                        @if ($errors->has('account_name'))<p class="text-danger"><small>{!!$errors->first('account_name')!!}</small></p> @endif
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
                <a href="{{ route('account', [str_replace('?', '', $qs)]) }}" class="btn btn-danger" role="button"><i class="fa fa-reply"></i>Back</a>
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>Save</button>
            </div>
            
        </div>
            {!! Form::hidden('id', $account->id) !!}    
        {!! Form::close() !!}
    </div>
</div>
@stop

@section('scripts')

@stop