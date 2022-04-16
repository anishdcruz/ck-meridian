<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{config('app.name')}}</title>
	<link rel="stylesheet" type="text/css" href="{{mix('css/frontend.css')}}">
</head>
<body>
	<div class="container" id="app">
		<nav class="navbar navbar-expand-lg navbar-light navbar-meridian">
			<a href="{{route('frontend.home')}}" class="navbar-brand">
				<img src="{{asset('img/meridian-logo.png')}}" align="Meridian" style="width: 200px;">
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse"
				data-target="#navbarLinks"
				>
					<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarLinks">
				<ul class="navbar-nav ml-auto">
					<li class="nav-item pr-3 ">
						<a class="nav-link" href="{{route('frontend.home')}}">{{__('app.home')}}</a>
					</li>
					<li class="nav-item pr-3">
						<a class="nav-link" href="{{route('frontend.features')}}">{{__('app.features')}}</a>
					</li>
					<li class="nav-item pr-3">
						<a class="nav-link" href="{{route('frontend.pricing')}}">{{__('app.pricing')}}</a>
					</li>
					<li class="nav-item pr-3">
						<a class="nav-link" href="{{route('login')}}">{{__('app.login')}}</a>
					</li>
				</ul>
				<a class="btn btn-outline-primary btn-sm" href="{{route('register')}}">{{__('app.sign_up')}}</a>
			</div>
		</nav>
	</div>
	@yield('content')
	<div class="mri-footer mt-5">
		<div class="container">
			<div class="row justify-content-md-between">
				<div class="col-sm-12 col-md-4 mb-4">
					<div>
						<h3 class="h5 mb-3">{{config('app.name')}}</h3>
                		<p>{{__('app.footer_about')}}</p>
					</div>
				</div>

				<div class="col-4 col-md-2 mb-4">
				    <ul class="nav flex-column">
				        <li class="mb-1">
				        	<a href="{{route('frontend.features')}}">{{__('app.features')}}</a>
				        </li>
				        <li class="mb-1">
				        	<a href="{{route('frontend.pricing')}}">{{__('app.pricing')}}</a>
				        </li>
				        <li class="mb-1">
				        	<a href="{{route('login')}}">{{__('app.login')}}</a>
				        </li>
				    </ul>
				</div>
				<div class="col-4 col-md-2 mb-4">
					<ul>
						<li class="mb-1">
							<a href="{{route('frontend.about')}}">{{__('app.about_us')}}</a>
						</li>
						<li class="mb-1">
							<a href="#">{{__('app.contact_us')}}</a>
						</li>
						<li class="mb-1">
							<a href="{{route('frontend.faqs')}}">{{__('app.faqs')}}</a>
						</li>
					</ul>
				</div>
				<div class="col-4 col-md-2 mb-4">
					<ul>
						<li class="mb-1">
							<a href="{{route('frontend.privacy')}}">{{__('app.privacy')}}</a>
						</li>
						<li class="mb-1">
							<a href="{{route('frontend.terms')}}">{{__('app.terms_of_service')}}</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="mri-footer-copy">
				<small>{{__('app.copy_right')}} &copy {{date('Y')}}</small>
			</div>
		</div>
	</div>
</body>
	<script type="text/javascript" src="{{mix('js/frontend.js')}}"></script>
</html>