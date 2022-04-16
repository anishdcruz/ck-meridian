<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Faq;
use App\Plan;

class FrontendController extends Controller
{
    public function home()
    {
    	return view('frontend.home');
    }

    public function features()
    {
    	return view('frontend.features');
    }

    public function pricing()
    {
        $faqs = Faq::where('show_on_pricing', 1)
            ->get();

        $plans = Plan::where('active', 1)
            ->orderBy('price', 'asc')
            ->get();

    	return view('frontend.pricing', [
            'faqs' => $faqs,
            'plans' => $plans
        ]);
    }

    public function faqs()
    {
        $faqs = Faq::get();
        return view('frontend.faqs', [
            'faqs' => $faqs
        ]);
    }

    public function privacy()
    {
        return view('frontend.privacy');
    }

    public function terms()
    {
        return view('frontend.terms');
    }

    public function about()
    {
        return view('frontend.about');
    }
}
