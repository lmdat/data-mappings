@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i>Mount Accounts to Item</h1>
        <p>Glue Account Code To Item Code</p>
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        @include('Backend::mappings-item.list-define-item', ['entries' => $entries, 'qs' => $qs, 'curr_id' => $item->id])
    </div>

    <div class="col-md-5">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'itemForm', 'id' => 'itemForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Mount Account
            </h4>
            <div class="tile-body">
                
                <div class="form-group">
                    <label class="control-label">Item</label>
                    {!! Form::text('item_name', $item->id . ' - ' . $item->item_name, ['id'=>'item_name', 'class' => 'form-control', 'readonly' => true]) !!}
                </div>

                <div class="form-group">
                    <label class="control-label">Account to Mount</label>
                <?php
                    $arr_selected = [];
                    $accounts = $item->accounts()->orderBy('account_code', 'DESC')->get();
                    foreach($accounts as $acc){
                        $arr_selected[] = $acc->account_code;
                    }
                ?>
                    {!! Form::select('mounted_account[]', $account_list, $arr_selected, ['class' => 'form-control', 'id' => 'mounted_account', "multiple"=>true]) !!}
                </div>
                
            </div>
            <div class="tile-footer text-right">
                    <a href="{{ route('mappings-item', [str_replace('?', '', $qs)]) }}" class="btn btn-danger" role="button"><i class="fa fa-reply"></i>Back</a>
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>Save</button>
            </div>
            
        </div>
        {!! Form::hidden('item_id', $item->id) !!}
        {!! Form::close() !!}
    </div>
</div>
@stop

@section('css-link')
{{--  <link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>  --}}
@stop

@section('js-link')
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
@stop


@section('scripts')
<script type="text/javascript">
    $(function(){
        $('#mounted_account').select2({
            width: '100%'
        });
    })
</script>
@stop