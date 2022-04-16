@extends('frontend.layouts')

@section('content')
	<div class="mri-page-title">
		<h1 class="display-3">{{__('app.about_us')}}</h1>
		<p class="lead">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
	</div>
	<div class="bg-skew">
		<div class="container mt-5">
			<div>
				<br><br><br>
				<img src="{{asset('/img/about-us.png')}}" style="width: 100%;">
				<br><br><br><br>
				<h3>Our Story</h3>
				<p class="lead">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
				tempor incididunt ut labore et dolore magna aliqua.</p>
				<p class="lead">Ut enim ad minim veniam,
				quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
				consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
				cillum dolore eu fugiat nulla pariatur.</p>
				<p class="lead">Excepteur sint occaecat cupidatat non
				proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				<br><br>
				<p class="display-4">Our team</p>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<img src="{{asset('/img/team/t1.png')}}" style="width: 100%;">
					<br><br>
					<h3>John Vare</h3>
					<p class="lead">Co-founder and CEO</p>
				</div>
				<div class="col-sm-4">
					<img src="{{asset('/img/team/t2.png')}}" style="width: 100%;">
					<br><br>
					<h3>Elise Telie</h3>
					<p class="lead">Co-founder</p>
				</div>
				<div class="col-sm-4">
					<img src="{{asset('/img/team/t3.png')}}" style="width: 100%;">
					<br><br>
					<h3>Shawn Doe</h3>
					<p class="lead">Software Developer</p>
				</div>
			</div>
		</div>
	</div>
	<div class="container mt-5 mri-get-started">
		<h4 class="mb-4">We are growing fast</h4>
		<p class="lead">Sent your resume to jobs@meridian.test</p>
	</div>
@endsection