@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i> Create/Edit Topic</h1>
        <p>Create Topic</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        @include('Backend::topic.list-topic', ['entries' => $entries, 'qs' => $qs])
    </div>

    <div class="col-md-4">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'itemForm', 'id' => 'itemForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Insert Topic
                {{--  @if(session()->has('error-message'))
                    <small><label class="badge badge-danger">Oh snap! {{ session()->get('error-message') }}</label></small>
                @endif

                @if(session()->has('success-message'))
                    <small><label class="badge badge-success">Yeah! {{ session()->get('success-message') }}</label></small>
                @endif  --}}
            </h4>
            <div class="tile-body">
                <div class="form-group">
                    <label class="control-label">Topic Type <span class="text-danger">*</span></label>
                    {!! Form::select('type_id', $type_list, '', ['class' => 'form-control', 'id' => 'type_id']) !!}
                    @if ($errors->has('type_id'))<p class="text-danger"><small>{!!$errors->first('type_id')!!}</small></p> @endif  
                    
                </div>

                <div class="form-group">
                    <label class="control-label">Parent Id</label>
                    {!! Form::select('parent_id', $tree_data, '', ['class' => 'form-control', 'id' => 'parent_id']) !!}
                    @if ($errors->has('parent_id'))<p class="text-danger"><small>{!!$errors->first('parent_id')!!}</small></p> @endif  
                    
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">Topic Name <span class="text-danger">*</span></label>
                        {!! Form::text('topic_name', '', ['id'=>'topic_name', 'class' => 'form-control']) !!}
                        @if ($errors->has('topic_name'))<p class="text-danger"><small>{!!$errors->first('topic_name')!!}</small></p> @endif  
                   </div>

                   <div class="col-md-6">
                         <label class="control-label">Short Name</label>
                         {!! Form::text('short_name', '', ['id'=>'short_name', 'class' => 'form-control']) !!}
                         @if ($errors->has('short_name'))<p class="text-danger"><small>{!!$errors->first('short_name')!!}</small></p> @endif  
                   </div>
                </div>

                <div class="animated-checkbox">
                    <label>
                        {!! Form::checkbox('show_multiple', '1', false, ['id' => 'chk_show_multiple']) !!}<span class="label-text">Insert multiple items?</span>
                    </label>
                </div>

                <div class="col-md-12" id="multiple_item_container">
                    <div class="form-group">
                        <small class="form-text text-muted">Use the fotmat: ITEM_NAME|SHORT_NAME or just ITEM_NAME</small>
                        {!! Form::textarea('multiple_item', '', ['id'=>'multiple_item', 'class' => 'form-control', 'placeholder' => 'ITEM_NAME|SHORT_NAME or just ITEM_NAME']) !!}
                        @if ($errors->has('multiple_item'))<p class="text-danger"><small>{!!$errors->first('multiple_item')!!}</small></p> @endif  
                    </div>
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

        $('#parent_id, #type_id').select2({
            width: '100%'
        });
        
        if($('#chk_show_multiple').is(':checked')){
            $('#multiple_item_container').show();
            $('#item_name').attr('disabled', true);
            $('#short_name').attr('disabled', true);
        }
        else{
            $('#item_name').attr('disabled', false);
            $('#short_name').attr('disabled', false);
            $('#multiple_item_container').hide();
        }

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