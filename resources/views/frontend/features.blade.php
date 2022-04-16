@extends('frontend.layouts')

@section('content')
	<div class="mri-page-title">
		<h1 class="display-3">{{__('app.features')}}</h1>
		<p class="lead">{{__('app.feature_description_2')}}</p>
	</div>
	<div class="mri-jumbotron">
		<div class="container">
			@foreach(config('meridian.features_in_detail') as $feature)
				@if($loop->iteration  % 2 == 1)
				    <div class="row">
				    	<div class="col-sm-6 offset-sm-1">
				    		<div class="mri-feature-pair-text">
				    			<div class="jumbotron">
				    				<h3 class="display-5 mb-4">{{$feature['title']}}</h3>
				    				<p class="lead">{{$feature['description']}}</p>
				    			</div>
				    		</div>
				    	</div>
				    	<div class="col-sm-4">
				    		<div class="mri-feature-pair-image">
				    			<img src="{{asset($feature['image'])}}" class="undraw-image">
				    		</div>
				    	</div>
				    </div>
				@else
					<div class="row">
						<div class="col-sm-4 offset-sm-1">
							<div class="mri-feature-pair-image">
				    			<img src="{{asset($feature['image'])}}" class="undraw-image">
				    		</div>
						</div>
						<div class="col-sm-6">
							<div class="mri-feature-pair-text">
				    			<div class="jumbotron">
				    				<h3 class="display-5 mb-4">{{$feature['title']}}</h3>
				    				<p class="lead">{{$feature['description']}}</p>
				    			</div>
				    		</div>
						</div>
					</div>
				@endif
			@endforeach
		</div>
	</div>
	@include('frontend.partials.trust')
@endsection