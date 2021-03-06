@extends('admin.app')

@section('title')
    Admin Dashboard 
@endsection

@section('content')
<div class="container">
    <div class="row">
        @include('admin.partials.sidebar')
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading">Admin Dashboard</div>

                <div class="panel-body">
                    Welcome {{ Auth::guard('admin')->user()->name }}!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
