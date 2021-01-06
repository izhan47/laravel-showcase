@extends('admin.layouts.app')

@section('content')
<section class="wag-login-page">
    <div class="row">
        <div class="col-sm-8 offset-sm-2 col-md-8 offset-md-2 col-lg-4 offset-lg-4">
            <div class="wag-admin-login-page">
                <div class="wag-admin-login-details-block">
                    <h2>Wag Enabled</h2>
                    <h3>Welcome back, Admin</h3>
                    <p class="lead">
                        Sign in to your account to continue
                    </p>
                    @if (Session::get('error'))
                        <span class="text-danger">{{ Session::get('error') }}</span>
                    @endif
                </div>
                <form class="" method="POST" action="{{ url('admin/login') }}" id="loginForm">
                    @csrf
                    <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                        <label>Email</label>
                        {{Form::text("email", null, ["class" => "form-control form-control-lg", "placeholder" => "Enter your email"])}}
                        @if ($errors->has('email'))
                            <span class="text-danger">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                        <label>Password</label>
                        {{Form::password("password", ["class" => "form-control form-control-lg", "placeholder" => "Enter your password"])}}
                        @if ($errors->has('password'))
                            <span class="text-danger">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="">
                        <button type="submit" class="wag-admin-btns-sign">Sign in</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
