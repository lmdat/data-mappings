@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i> Define Mappings Item</h1>
        <p>Import data of item from excel or csv file</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        @include('Backend::mappings-item.list-define-item', ['entries' => $entries, 'qs' => $qs, 'curr_id' => $item->id])
    </div>

    <div class="col-md-4">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'put', 'name' => 'itemForm', 'id' => 'itemForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Edit Item
                {{--  @if(session()->has('error-message'))
                    <small><label class="badge badge-danger">Oh snap! {{ session()->get('error-message') }}</label></small>
                @endif

                @if(session()->has('success-message'))
                    <small><label class="badge badge-success">Yeah! {{ session()->get('success-message') }}</label></small>
                @endif  --}}
            </h4>
            <div class="tile-body">
                <div class="form-group">
                    <label class="control-label">Mappings Type</label>
                    {!! Form::select('type_id', $type_list, $item->type_id, ['class' => 'form-control', 'id' => 'type_id', 'required']) !!}
                    @if ($errors->has('type_id'))<p class="text-danger"><small>{!!$errors->first('type_id')!!}</small></p> @endif  
                </div>

                <div class="form-group">
                    <label class="control-label">Parent Id</label>
                    {!! Form::select('parent_id', $tree_data, $item->parent_id, ['class' => 'form-control', 'id' => 'parent_id']) !!}
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">Item Name</label>
                        {!! Form::text('item_name', $item->item_name, ['id'=>'item_name', 'class' => 'form-control', 'required']) !!}
                        @if ($errors->has('item_name'))<p class="text-danger"><small>{!!$errors->first('item_name')!!}</small></p> @endif  
                   </div>

                   <div class="col-md-6">
                         <label class="control-label">Short Name</label>
                         {!! Form::text('short_name', $item->short_name, ['id'=>'short_name', 'class' => 'form-control', 'required']) !!}
                         @if ($errors->has('short_name'))<p class="text-danger"><small>{!!$errors->first('short_name')!!}</small></p> @endif  
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
                <a href="{{ route('mappings-item', [str_replace('?', '', $qs)]) }}" class="btn btn-danger" role="button"><i class="fa fa-reply"></i>Back</a>
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>Save</button>
            </div>
            
        </div>
            {!! Form::hidden('id', $item->id) !!}    
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