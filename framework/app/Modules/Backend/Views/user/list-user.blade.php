    
    <div class="tile">
        <h4 class="tile-title">
            User List
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
                        <th>Id</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Permission</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $k=>$item)
                    <tr @if($item->id == @$curr_id) class='table-primary' @endif>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->first_name }}</td>
                        <td>{{ $item->last_name }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{$item->getMaxRoleName()}}</td>
                        <td>
                        <?php
                            $coms = $item->companies()->orderBy('company_name', 'ASC')->get();
                            $n = count($coms);
                        ?>
                        @for($i=0; $i<$n; $i++)
                            <small>{{$coms[$i]->short_name}}</small>&nbsp;
                            @if($i % $n == 2) <br/> @endif
                        @endfor
                        </td>
                        <td>
                        @if($item->status == 1)
                            {{--  <span class="badge badge-info">On</span>  --}}
                            <a href="{{ route('user-get-status', ['id' => $item->id, str_replace('?', '', $qs)]) }}" class="badge badge-info">On</a>
                        @else
                            {{--  <span class="badge badge-secondary">Off</span>  --}}
                            <a href="{{ route('user-get-status', ['id' => $item->id, str_replace('?', '', $qs)]) }}" class="badge badge-secondary">Off</a>
                        @endif
                        </td>
                        <td>
                            <a href="{{ route('user-edit', ['id' => $item->id, str_replace('?', '', $qs)]) }}" class="btn btn-sm btn-warning" role="button"><i class="fa fa-edit"></i>Edit</a>
                            <a href="{{ route('user-assign-company', ['id' => $item->id, str_replace('?', '', $qs)]) }}" class="btn btn-sm btn-primary" role="button"><i class="fa fa-link"></i>Assign</a>
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
   

    
