@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i> Manage User</h1>
        <p>Create/Edit/Select User</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        @include('Backend::company.list-company', ['entries' => $entries, 'qs' => $qs, 'curr_id' => $com->id])
    </div>

    <div class="col-md-4">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'put', 'name' => 'userForm', 'id' => 'userForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Edit Company
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
                        <label class="control-label">Company Name</label>
                        {!! Form::text('company_name', $com->company_name, ['id'=>'company_name', 'class' => 'form-control', 'autofocus']) !!}
                    </div>

                    <div class="col-md-6">
                            <label class="control-label">Short Name</label>
                            {!! Form::text('short_name', $com->short_name, ['id'=>'short_name', 'class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">Phone</label>
                        {!! Form::text('phone', $com->phone, ['id'=>'phone', 'class' => 'form-control']) !!}
                    </div>

                    <div class="col-md-6">
                            <label class="control-label">Mobile</label>
                            {!! Form::text('mobile', $com->mobile, ['id'=>'mobile', 'class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Be handle by</label>
                    <?php
                        $arr_selected = [];
                        $user_list = $com->users()->orderBy('first_name')->get();
                        foreach($user_list as $user){
                            $arr_selected[] = $user->id;
                        }
                        // dd($arr_selected);
                    ?>
                    {!! Form::select('user_id[]', $users, $arr_selected, ['class' => 'form-control', 'id' => 'user_id', 'multiple']) !!}
                    
                </div>
                
            </div>
            <div class="tile-footer text-right">
                    <a href="{{ route('company', [str_replace('?', '', $qs)]) }}" class="btn btn-danger" role="button"><i class="fa fa-reply"></i>Back</a>
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>Save</button>
            </div>
            
        </div>
            {!! Form::hidden('id', $com->id) !!}
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