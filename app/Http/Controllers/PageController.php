<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Coupon;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Product;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about', ['categoryCount' => \App\Models\Category::active()->count()]);
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function sendMessage(Request $request)
    {
        $request->merge(['phone' => normalize_phone($request->input('phone'))]);
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'regex:/^01[3-9]\d{8}$/'],
            'email' => ['nullable', 'email', 'max:190'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'website' => ['prohibited'], // honeypot
        ], [
            'name.*' => 'Please enter your name.',
            'phone.*' => 'Enter a valid Bangladeshi mobile number.',
            'message.*' => 'Please write a message (at least 10 characters).',
        ]);
        ContactMessage::create($data);

        return $this->done($request, 'Thank you! We’ll get back to you soon.');
    }

    public function subscribe(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'max:190']], ['email.*' => 'Please enter a valid email address.']);
        Subscriber::firstOrCreate(['email' => strtolower(trim($request->input('email')))]);

        return $this->done($request, 'Thank you for subscribing!');
    }

    public function faq()
    {
        return view('pages.faq', ['groups' => Faq::active()->get()->groupBy('group')]);
    }

    public function offers()
    {
        return view('pages.offers', [
            'coupons' => Coupon::visible()->get(),
            'sale' => Product::with('category')->active()->whereNotNull('sale_price')->where('sale_price', '>', 0)
                ->whereColumn('sale_price', '<', 'price')->latest()->get(),
        ]);
    }

    public function policy()
    {
        return view('pages.policy', ['page' => Page::where('slug', 'policy')->first()]);
    }

    public function sizeGuide()
    {
        return $this->page('size-guide');
    }

    public function terms()
    {
        return $this->page('terms');
    }

    private function page(string $slug)
    {
        return view('pages.content', ['page' => Page::where('slug', $slug)->firstOrFail()]);
    }
}
