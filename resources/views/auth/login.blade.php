@extends('app')

@section('content')

    <div class="container-fluid">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-default">
					<div class="panel-heading">Login</div>

					<div class="panel-body">
						@if ($errors->any())
							<div class="alert alert-danger">
								<strong>Whoops!</strong> There were some problems with your input.

								@if ($errors->has('global'))
									<br><br>
									<ul>
										@foreach ($errors->get('global') as $error)
											<li>{{ $error }}</li>
										@endforeach
									</ul>
								@endif
							</div>
						@endif

						{!! Form::open(['class' => 'form-horizontal']) !!}

						    <div class="form-group">
						        {!! Form::label('email', 'Email', ['class' => 'col-sm-4 control-label']) !!}
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
							    <div class="col-sm-offset-4 col-sm-6">
							        <div class="checkbox">
							            <label for="remember">
							                {!! Form::checkbox('remember', null, null, ['id' => 'remember']) !!} Remember Me
							            </label>
							        </div>
							        <small class="text-danger">{{ $errors->first('remember') }}</small>
							    </div>
							</div>

							<div class="form-group">
								<div class="col-sm-6 col-sm-offset-4">
									{!! Form::submit('Submit', ['class' => 'btn btn-primary submit']) !!}

									<div class="btn-links">
										<a href="{{ url('password/forgot') }}" class="btn btn-link">Recover Password</a>
										<a href="{{ url('register/resend') }}" class="btn btn-link">Resend Confirmation</a>
									</div>
								</div>
							</div>

						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection
