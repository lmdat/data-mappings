    
    <div class="tile">
        <h4 class="tile-title">
            Account List
            @if(session()->has('error-message'))
                <small><label class="badge badge-danger">Oh snap! {{ session()->get('error-message') }}</label></small>
            @endif

            @if(session()->has('success-message'))
                <small><label class="badge badge-success">Yeah! {{ session()->get('success-message') }}</label></small>
            @endif
        </h4>
        <div class="tile-body">
            <table class="table table-hover table-bordered" id="item_table">
                <thead class="thead-dark">
                    <tr>
                        <th>Code</th>
                        <th>Account Name</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $k=>$item)
                    <tr @if($item->id == @$curr_id) class='table-primary' @endif>
                        <td>{{ $item->account_code }}</td>
                        <td>{{ $item->account_name }}</td>
                        <td>
                        @if($item->status == 1)
                            {{--  <span class="badge badge-info">On</span>  --}}
                            <a href="{{route('account-get-status', ['id' => $item->id, str_replace('?', '', $qs)])}}" class="badge badge-info">On</a>
                        @else
                            {{--  <span class="badge badge-secondary">Off</span>  --}}
                            <a href="{{route('account-get-status', ['id' => $item->id, str_replace('?', '', $qs)])}}" class="badge badge-secondary">Off</a>
                        @endif
                        </td>
                        <td>
                        <a href="{{ route('account-edit', ['id' => $item->id, str_replace('?', '', $qs)]) }}" class="btn btn-sm btn-warning" role="button"><i class="fa fa-edit"></i>Edit</a>
                        
                            
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
   

    
