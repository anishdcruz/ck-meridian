<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{config('app.name')}}</title>
        <link rel="stylesheet" type="text/css" href="{{mix('css/frontend.css')}}">
    </head>
    <body>
    	<br><br><br>
    	@if($user->subscribed('main'))
    		<div class="container" id="app">
    		    <div class="row justify-content-center">
    		        <div class="col-md-8">
    		            <div class="card card-register shadow-lg mb-4">
    		                <div class="card-header text-center">

    		                    {{ __('app.subscribe_now') }}
    		                    @if(!Meridian::isCardUpFrontRequired())
    		                        <br>
    		                        <small>No credit card needed up front</small>
    		                    @endif
    		                </div>

    		                <div class="card-body">
    		                    <form method="POST" action="{{ route('app.no_team_create_team') }}" id="register">
    		                        @csrf
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
    	@else

    		<div class="container" id="app">
    		    <div class="row justify-content-center">
    		        <div class="col-md-8">
    		            <div class="card card-register shadow-lg mb-4">
    		                <div class="card-header text-center">

    		                    {{ __('app.subscribe_now') }}
    		                    @if(!Meridian::isCardUpFrontRequired())
    		                        <br>
    		                        <small>No credit card needed up front</small>
    		                    @endif
    		                </div>

    		                <div class="card-body">
    		                    <form method="POST" action="{{ route('app.no_team_subscription') }}" id="register">
    		                        @csrf
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
    		                                    {{ __('app.subscribe_now') }}
    		                                </x-button>
    		                            </div>
    		                        </div>
    		                    </form>
    		                </div>
    		            </div>
    		        </div>
    		    </div>
    		</div>
    		<script src="https://js.stripe.com/v3/"></script>
    	@endif
    	<script type="text/javascript" src="{{mix('js/frontend.js')}}"></script>
    </body>
</html>

