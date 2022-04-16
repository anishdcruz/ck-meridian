@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-register shadow-lg mb-4">
                <div class="card-header text-center">

                    {{ __('Sign Up') }}
                    @if(!Meridian::isCardUpFrontRequired())
                        <br>
                        <small>No credit card needed up front</small>
                    @endif
                    @if(config('meridian.in_demo'))
                    <br><br>
                    	<div class="alert alert-info">Cannot Sign up in Demo Mode!</div>
                    @endif
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}" id="register">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label for="company_name" class="col-md-4 col-form-label text-md-right">{{ __('Company Name') }}</label>

                            <div class="col-md-6">
                                <input id="company_name" type="text" class="form-control{{ $errors->has('company_name') ? ' is-invalid' : '' }}" name="company_name" value="{{ old('company_name') }}" required autofocus>

                                @if ($errors->has('company_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('company_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="plan" class="col-md-4 col-form-label text-md-right">{{ __('Plan') }}</label>

                            <div class="col-md-6">
                                <select id="plan" type="text" class="form-control{{ $errors->has('plan') ? ' is-invalid' : '' }}" name="plan" required>
                                    @foreach(Meridian::plans() as $plan)
                                        <option value="{{$plan->id}}"
                                            {{request('plan_id') && request('plan_id') == $plan->id ? 'selected' : ''}}>
                                            {{$plan->name}} (${{$plan->price}} / {{$plan->time_period}})
                                        </option>
                                    @endforeach
                                </select>

                                @if ($errors->has('plan'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('plan') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if(Meridian::isCardUpFrontRequired())
                            <div class="form-group row">
                                <label for="card-element" class="col-md-4 col-form-label text-md-right">{{ __('Credit Card') }}</label>

                                <div class="col-md-6">
                                    <stripe-card
                                        api-key="{{config('services.stripe.key')}}"
                                        form="register"
                                        >
                                    </stripe-card>
                                </div>
                            </div>
                        @endif
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="terms"> I Accept the <a href="{{route('frontend.terms')}}" target="_blank">Terms</a> and <a href="{{route('frontend.privacy')}}" target="_blank">Privacy</a>
                                    </label>
                                </div>
                                @if ($errors->has('terms'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('terms') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <x-button>
                                    {{ __('Register') }}
                                </x-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts_before')
    <script src="https://js.stripe.com/v3/"></script>
@endpush