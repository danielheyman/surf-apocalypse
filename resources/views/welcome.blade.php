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
    					<a href="{{ url('password/forgot') }}">Recover password</a>  |  <a href="{{ url('register') }}">Register New</a>
    				</div>
    			</div>
            </div>
		</div>
	</section>

    <section class="benefits">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <h2>BENEFIT ONE</h2>
                    <p>Surf Apocalypse is on the cutting edge of TE programs. Endorsed and promoted by some of the biggest names in internet marketing world, Surf Apocalypse is destined to be a player in the TE world.</p>
                </div>
                <div class="col-md-3 col-sm-6">
                    <h2>Benefit Two</h2>
                    <p>Fast and friendly customer service to earning real cash and commissions on upgrades and credit purchases. Surf Apocalypse advertising is fast paced and a great promotional tool.</p>
                </div>
                <div class="col-md-3 col-sm-6">
                    <h2>Benefit Three</h2>
                    <p>Earn both traffic and cash from your promotional efforts. And with our custom built Click and Surf System you earn Traffic every time you visit another member's webpage.</p>
                </div>
                <div class="col-md-3 col-sm-6">
                    <h2>Benefit Four</h2>
                    <p>Earn credits from your own referral's surfing . Each time they surf for credits you will automatically earn credits too. The more members you refer, the more credits and commission you earn.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="join">
        <div class="container">
            <div class="row header">
                If you're not using the Surf Apocalypse Manual Traffic exchange you're leaving 100's and 1000's of subscribers and Referrals on the table!
            </div>
            <div class="row register">
                <div class="col-lg-6 col-lg-offset-6 col-md-7 col-md-offset-5">
                    <img src="{{ asset('img/joinnow-title.png') }}" alt="Join Now!" />
					<p>Join now and be a part of a system that allows you to earn both traffic and cash from your promotional efforts.</p>

                    {!! Form::open(['class' => 'form-horizontal', 'url' => 'register']) !!}

                        <div class="form-group">
                            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Name']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Email']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::password('password', ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Password']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::password('password_confirmation', ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Confirm password']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::checkbox('terms', null, null, ['required' => 'required']) !!} Agree to Terms
                        </div>

                        <div class="form-group">
                            {!! Form::submit('Register', ['class' => 'btn btn-primary']) !!}
                        </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 logo">
                    <img class="footer-logo" src="{{ asset('img/logo.png') }}" alt="SURF APOCALYPSE" />
                </div>
                <div class="col-lg-6 footer-menu">
                    <ul>
    					<li><a href="">HOME</a></li>
    					<li><a href="">PRIVACY</a></li>
    					<li><a href="">FAQ</a></li>
    					<li><a href="">JOIN</a></li>
    					<li><a href="">TERMS & CONDITIONS</a></li>
    					<li><a href="">LOGIN</a></li>
    				</ul>
    				<p class="copyright">&copy; 2015 www.SurfApocalypse.com all rights reserved worldwide</p>
                </div>
            </div>
        </div>
    </footer>

@endsection
