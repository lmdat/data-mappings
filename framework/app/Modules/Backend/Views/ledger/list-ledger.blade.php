@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-location-arrow"></i> Define Dimension</h1>
        <p>Import data of Dimension</p>
    </div>
</div>

<div class="row">
   
    <div class="col-md-12">
        {!! Form::open(['url' => $form_uri . $qs, 'method' => 'post', 'name' => 'ledgerForm', 'id' => 'ledgerForm', 'role' => 'form', 'files' => false]) !!}
        <div class="tile">
            <h4 class="tile-title">
                Ledger List
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
                            <th>Ledger Key</th>
                            <th>Base Amount</th>
                            <th>Period</th>
                            
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $k=>$item)
                        <tr>
                            <td>{{ $item->ledger_key }}</td>
                            <td class="text-right">{{ App\Libs\Utils\Vii::formatCurrency($item->base_amount) }}</td>
                            <td>{{ substr($item->accounting_period, 0, strlen($item->accounting_period) - 3 ) }}</td>
                            <td>
                            {{--  <a href="{{ route('dimension-edit', ['id' => $item->id, str_replace('?', '', $qs)]) }}" class="btn btn-sm btn-warning" role="button"><i class="fa fa-edit"></i>Edit</a>  --}}
                            
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

@section('scripts')

@stop   
    
   

    
