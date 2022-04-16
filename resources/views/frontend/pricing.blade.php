@extends('frontend.layouts')

@section('content')
	<div class="mri-page-title">
		<h1 class="display-3">{{__('app.pricing')}}</h1>
		@if(!Meridian::isCardUpFrontRequired())
			<p class="lead">{{__('no_card_upfront')}}</p>
		@else
			<p class="lead">{{__('app.simple_pricing')}}</p>
		@endif
	</div>
	<div class="bg-skew">
			<div class="mri-pricing">
				<div class="container">
					<div class="row">
		    			@foreach($plans as $plan)
		    				<div class="col-md-4">
		    					<div class="card card-price shadow-lg mb-4">
		    						<div class="card-header">
		    						    <h3 class="h4 font-weight-normal">{{$plan->name}}</h3>
		    						    <span class="d-block h1 my-2"><span class="h2">$</span>{{$plan->price}}</span>
		    						    <span class="d-block text-muted">{{$plan->time_peroid}}</span>
		    						</div>
								    <div class="card-body">
								        <ul class="list-unstyled mb-4">
			                                @foreach($plan->list as $item)
								               <li class="py-2 text-secondary">{{$item}}</li>
			                                @endforeach
								        </ul>
								    	<a class="btn btn-pill btn-primary mb-3"
			                            href="{{route('register')}}?plan_id={{$plan->id}}">Get started</a>
								    </div>
		    					</div>
		    				</div>
		    			@endforeach
		    		</div>
				</div>
			</div>
			<div class="container mt-5">
				<div class="pt-5">
					<h1 class="display-5 text-center">{{__('app.faq_title')}}</h1>
					<hr style="width: 100px;">
				</div>
				<div class="mri-faqs">
					@foreach($faqs as $faq)
						<div class="mri-faq-item">
							<p class="mri-faq-q">{{$faq->question}}</p>
							<p class="mri-faq-a">{{$faq->answer}}</p>
						</div>
					@endforeach
				</div>
			</div>
	</div>
@endsection