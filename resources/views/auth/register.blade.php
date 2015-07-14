@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Register</div>
				<div class="panel-body">
					@if (session('status'))
						<div class="alert alert-success">
							{{ session('status') }}
						</div>
					@endif

					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<strong>Whoops!</strong> There were some problems with your input.
						</div>
					@endif

					{!! Form::open(['class' => 'form-horizontal']) !!}

						<div class="form-group">
						    {!! Form::label('name', 'Name', ['class' => 'col-sm-4 control-label']) !!}
							<div class="col-sm-6">
						    	{!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
						    	<small class="text-danger">{{ $errors->first('name') }}</small>
							</div>
						</div>

						<div class="form-group">
						    {!! Form::label('email', 'Email', ['class' =>'col-sm-4 control-label']) !!}
						    <div class="col-sm-6">
						    	{!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
						    	<small class="text-danger">{{ $errors->first('email') }}</small>
							</div>
						</div>

						<div class="form-group">
						    {!! Form::label('password', 'Password', ['class' => 'col-sm-4 control-label']) !!}
						    <div class="col-sm-6">
							    {!! Form::password('password', ['class' => 'form-control', 'required' => 'required']) !!}
							    <small class="text-danger">{{ $errors->first('password') }}</small>
							</div>
						</div>

						<div class="form-group">
						    {!! Form::label('password_confirmation', 'Confirm Password', ['class' => 'col-sm-4 control-label']) !!}
						    <div class="col-sm-6">
							    {!! Form::password('password_confirmation', ['class' => 'form-control', 'required' => 'required']) !!}
							    <small class="text-danger">{{ $errors->first('password_confirmation') }}</small>
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm-6 col-sm-offset-4">
								{!! Form::submit('Register', ['class' => 'btn btn-primary']) !!}
							</div>
						</div>

					{!! Form::close() !!}

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
