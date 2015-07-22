@extends('app')

@section('content')

    <section class="banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-lg-offset-5 col-md-8 col-md-offset-4 col-sm-10 col-sm-offset-2">
                    <h1 class="fadeInRight animated">
                        <span>Get A Horde Of Traffic</span><br>
                        <span>And Leads Today!</span>
                    </h1>

                    <h6>We Bring You Legions Of Highly Targeted Subscribers And Leads Directly To Your Website 24hrs A Day, 7 Days A Week!</h6>

                    <p>
                        100% FREE: You just can’t beat no-cost online advertising.<br>
                        It’s Viral: Traffic increases automatically and exponentially<br>
                        Earn REAL CASH COMMISSIONS when your referrals purchase extra credits or services.<br>
                        It’s Proven: Thousands of members are benefiting from Promoting multiple web pages <br>
                        Highly Targeted: You’ll only get live, real-time targeted traffic. Real people visiting your site. <br>
                        Downline Builder: All your signups get to join your programms through your link
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="login">
        <div class="container">
    		<div class="row">
    			<div class="col-md-7 info">
					<h5>Surf Apocalypse Is Simply The Best <span>NEW</span> Manual Exchange!</h5>
					<p>We Specialize in delivering Top Notch Traffic To Your Website Whenever You Need It!</p>
    			</div>
    			<div class="col-md-5 form">
                    {!! Form::open(['method' => 'POST', 'url' => 'login', 'class' => 'form-horizontal']) !!}
                        <div class="row">
                            <div class="col-md-5">
                                {!! Form::text('email', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Email']) !!}
                            </div>
                            <div class="col-md-5">
                                {!! Form::password('password', ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Password']) !!}
                            </div>
                            <div class="col-md-2">
                                {!! Form::submit("Login", ['class' => 'btn btn-success']) !!}
                            </div>
                        </div>
                    {!! Form::close() !!}

    				<div class="more">
    					<a href="#">Recover password</a>  |  <a href="{{ url('register') }}">Register New</a>
    				</div>
    			</div>
            </div>
		</div>
	</section>

    {{-- <div class="container">
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
    </div> --}}

@endsection
