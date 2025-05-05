@extends('frontend.frontend_dashboard')
@section('main')
@section('title')
 Login Or Register | Easy RealEstate  
@endsection

<!--Page Title-->
<section class="page-title-two bg-color-1 centred">
    <div class="pattern-layer">
        <div class="pattern-1" style="background-image: url({{ asset('frontend/assets/images/shape/shape-9.png') }});"></div>
        <div class="pattern-2" style="background-image: url({{ asset('frontend/assets/images/shape/shape-10.png') }});"></div>
    </div>
    <div class="auto-container">
        <div class="content-box clearfix">
            <h1>Sign In / Register</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li>Sign In / Register</li>
            </ul>
        </div>
    </div>
</section>
<!--End Page Title-->

<!-- ragister-section -->
<section class="ragister-section centred sec-pad">
    <div class="auto-container">
        <div class="row clearfix">
            <div class="col-xl-8 col-lg-12 col-md-12 offset-xl-2 big-column">
                <div class="tabs-box">
                    <div class="tab-btn-box">
                        <ul class="tab-btns tab-buttons centred clearfix">
                            <li class="tab-btn active-btn" data-tab="#tab-1">Login</li>
                            <li class="tab-btn" data-tab="#tab-2">Register</li>
                        </ul>
                    </div>
                    <div class="tabs-content">
                        {{-- Login Tab --}}
                        <div class="tab active-tab" id="tab-1">
                            <div class="inner-box">
                                <h4>Sign in</h4>
                                {{-- Display Login Errors --}}
                                @if ($errors->has('login') || $errors->has('password'))
                                <div class="alert alert-danger">
                                    <ul>
                                        @if ($errors->has('login'))
                                            <li>{{ $errors->first('login') }}</li>
                                        @endif
                                        @if ($errors->has('password') && !$errors->has('login'))
                                            <li>{{ $errors->first('password') }}</li>
                                        @endif
                                        @if (session('status'))
                                            <li>{{ session('status') }}</li> 
                                        @endif
                                    </ul>
                                </div>
                                @endif
                                 @if (session('status'))
                                    <div class="alert alert-danger">
                                         {{ session('status') }} 
                                    </div>
                                @endif

                                <form action="{{ route('login') }}" method="post" class="default-form">
                                    @csrf

                                    <div class="form-group">
                                        <label>Email/Name/Phone </label>
                                        <input type="text" name="login" id="login" required="" value="{{ old('login') }}">
                                         @error('login')
                                            <span class="text-danger">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                     
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" name="password" id="password" required="">
                                         @error('password')
                                            <span class="text-danger">{{ $message }}</span> 
                                        @enderror
                                    </div>

                                    <div class="form-group message-btn">
                                        <button type="submit" class="theme-btn btn-one">Sign in</button>
                                    </div>
                                </form>
                                <div class="othre-text">
                                     {{-- Add Forgot Password link if needed later --}}
                                    {{-- <p> <a href="#">Forgot Password?</a></p> --}}
                                </div>
                            </div>
                        </div>

                        {{-- Register Tab --}}
                        <div class="tab" id="tab-2">
                            <div class="inner-box">
                                <h4>Register</h4>
                                {{-- Display Registration Errors (Check errors specific to registration fields) --}}
                                @if ($errors->has('name') || $errors->has('email') || $errors->has('password') || $errors->has('role') || $errors->has('password_confirmation'))
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <form action="{{ route('register') }}" method="post" class="default-form">
                                    @csrf

                                    <div class="form-group">
                                        <label>User name</label>
                                        <input type="text" name="name" id="name" required="" value="{{ old('name') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Email address</label>
                                        <input type="email" name="email" id="email" required="" value="{{ old('email') }}">
                                    </div>

                                    {{-- Role Dropdown --}}
                                    <div class="form-group">
                                        <label>Register As</label>
                                        <select name="role" class="form-control" required>
                                            <option value="" selected disabled>Select Role...</option>
                                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                                            <option value="agent" {{ old('role') == 'agent' ? 'selected' : '' }}>Agent</option>
                                            {{-- Optionally hide admin or handle separately if needed --}}
                                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option> 
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" name="password" id="password" required="">
                                    </div>

                                     <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" required="">
                                    </div>

                                    <div class="form-group message-btn">
                                        <button type="submit" class="theme-btn btn-one">Register</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ragister-section end -->

@endsection