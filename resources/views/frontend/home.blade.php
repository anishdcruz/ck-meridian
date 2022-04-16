@extends('frontend.layouts')

@section('content')
	<div class="mri-jumbotron">
		<div class="container">
			<div class="row">
				<div class="col-sm-6">
					<div class="jumbotron">
						<h1 class="display-4 mb-4">{{__('app.title_1')}}</h1>
						<p class="lead">{{__('app.description_1')}}</p>
						<div class="mt-3">
							<p class="lead">
								<a href="{{route('register')}}" class="btn btn-success btn-lg">{{__('app.get_started')}}</a>
								<a href="{{route('frontend.pricing')}}" class="btn btn-outline-secondary btn-lg">{{__('app.see_pricing')}}</a>
							</p>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<img src="{{asset('img/undraw_printing_invoices_5r4r.png')}}" class="undraw-image">
				</div>
			</div>
		</div>
	</div>
	<div class="bg-skew">
		<div class="mri-features">
			<div class="container">
				<div class="text-center w-50 mx-auto">
					<h2>{{__('app.awesome_features')}}</h2>
					<p class="lead">{{__('app.feature_description_1')}}</p>
				</div>
				<div class="mri-feature-list">
					<div class="row">
						@foreach(config('meridian.features') as $feature)
						<div class="col-sm-4">
							<div class="mri-feature-item">
								<img src="{{asset($feature['image'])}}" class="mri-feature-img">
								<strong>{{$feature['title']}}</strong><br>
								<p>{{$feature['description']}}</p>
							</div>
						</div>
						@endforeach
					</div>
					<div class="text-center mt-5 mb-5 pb-5">
						<a href="{{route('frontend.features')}}" class="btn btn-outline-primary">{{__('app.see_all_features')}}</a>
						<br><br><br>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="text-center w-50 mx-auto">
		<h2>{{__('app.testimonials')}}</h2>
		<p class="lead">{{__('app.testimonial_desc')}}</p>
	</div>
	<div class="mri-carousel">
		<div id="featureCustomers" class="carousel slide" data-ride="carousel">
			<div class="carousel-inner">
				@foreach(config('meridian.customers') as $customer)
					<div class="carousel-item {{isset($customer['active']) ? 'active' : ''}}">
						<img class="d-block w-100" src="{{asset('img/glass-1.png')}}" alt="First slide">
						<div class="carousel-caption d-none d-md-block">
							<img class="mx-auto d-block rounded-circle img-thumbnail" style="width: 160px;" src="{{asset($customer['image'])}}">
							<div class="mt-3 mb-4">
								<strong>{{$customer['name']}}</strong> | <span>{{$customer['company']}}</span>
							</div>
							<blockquote class="blockquote">
								<p class="mb-0">
									<em>{{$customer['text']}}</em>
								</p>
							</blockquote>
						</div>
					</div>
				@endforeach
			</div>
			<a class="carousel-control-prev" href="#featureCustomers"
				role="button" data-slide="prev">
				<span class="carousel-control-prev-icon"></span>
			</a>
			<a class="carousel-control-next" href="#featureCustomers"
				role="button" data-slide="next">
				<span class="carousel-control-next-icon"></span>
			</a>
		</div>
	</div>
	@include('frontend.partials.trust')
@endsection