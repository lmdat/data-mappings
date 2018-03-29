@extends('Backend::layouts.master')

@section('title')
    {{$page_title}}
@stop

@section('content')

<div class="app-title">
    <div>
      <h1><i class="fa fa-location-arrow"></i> Dashboard</h1>
      <p>General Infomartion</p>
    </div>
    {{--  <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item"><a href="#">Form Components</a></li>
    </ul>  --}}
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        Welcome {{$user}}
        
      </div>
    </div>
  </div>

@stop