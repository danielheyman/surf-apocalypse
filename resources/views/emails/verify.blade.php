<p>Welcome {{ $user->name }},<p>

<p>Click <a href="{{ url('register/verify/'.$user->confirmation_code) }}">here</a> to confirm your account.</p>

<p>Thanks</p>
