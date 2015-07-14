@extends('app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                @if(session('message')) <div class="alert alert-info" role="alert">{{ session('message') }}</div> @endif

                <div class="panel panel-default">
                    <div class="panel-heading">Home</div>

                    <div class="panel-body">
                        You are logged in!
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
