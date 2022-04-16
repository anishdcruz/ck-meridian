@extends('frontend.layouts')

@section('content')
	<div class="mri-page-title">
		<h1 class="display-3">{{__('app.faq_title')}}</h1>
		<p class="lead">{{__('app.faq_description')}}</p>
	</div>
	<div class="bg-skew">
		<div class="container mt-5">
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