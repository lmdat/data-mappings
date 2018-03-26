@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i> Revision</h1>
        <p>Manage revision of Ledger</p>
    </div>
</div>

<div class="row">
   
    <div class="col-md-12">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'ledgerForm', 'id' => 'ledgerForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Ledger Revision List
                @if(session()->has('error-message'))
                    <small><label class="badge badge-danger">Oh snap! {{ session()->get('error-message') }}</label></small>
                @endif
    
                @if(session()->has('success-message'))
                    <small><label class="badge badge-success">Yeah! {{ session()->get('success-message') }}</label></small>
                @endif
            </h4>
            <div class="tile-body">
                <div class="row">
                    <div class="col-md-4">
                        {!! Form::select('upload_id', $upload_entries, $upload_id_selected, ['class' => 'form-control', 'id' => 'upload_id', "multiple"=>false]) !!}
                    </div>
                    
                </div>
                <hr/>
                <table class="table table-hover table-bordered" id="item_table">
                    <thead class="thead-dark">
                        <tr>
                            <th>Id</th>
                            <th>Upload Title</th>
                            <th>Revision Number</th>
                            <th>File name</th>
                            <th>Created at</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revisions as $k=>$item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->upload->upload_title }}</td>
                            <td class="text-right">Revision {{ $item->revision_number }}</td>
                            <td><a href="{{ route('revision-download', ['id' => $item->id]) }}">{{ basename($item->file_path) }}</a></td>
                            <td>{{ App\Libs\Utils\Vii::formatDateTime($item->created_at) }}</td>
                            <td>
                            <a id="delete_revision" href="{{ route('revision-delete', ['id' => $item->id, str_replace('?', '', $qs)]) }}" class="btn btn-sm btn-danger" role="button"><i class="fa fa-trash"></i>Delete</a>
                            
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            
                <div class="row">
                    <div class="col-md-5">
                        <span>
                            {{ "Displaying {$entries->firstItem()} to {$entries->lastItem()} of {$entries->total()} entries." }}
                        </span>
                    </div>
    
                    <div class="col-md-7">
                        <div class="pull-right">
                            {!! $entries->links() !!}
                        </div>
                        
                        {{--  <div class="pull-right">  --}}
                        {{--  <ul class="pagination justify-content-end">
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                </li>
                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </li>
                        </ul>  --}}
                        {{--  </div>  --}}
                    </div>
                
                </div>
            </div>
            {{--  <div class="tile-footer"><a class="btn btn-primary" href="#">Link</a></div>  --}}
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
        $('#upload_id').select2({
            width: '100%'
        });

        $('#upload_id').on('change', function(e){
            var id = $(this).val();
            window.location.href = "{{ $upload_change_url }}" + "&upid=" + id
        });

        $('#delete_revision').on('click', function(e){
            e.preventDefault();
            // alert($(this).attr('href'));
            if(confirm('Are you sure want to delete this revision?')){
                window.location.href = $(this).attr('href');
            }
        });
    })
</script>
@stop   
    
   

    
