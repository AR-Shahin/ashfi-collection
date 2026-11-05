@extends('layouts.app')
@section('page', 'contact')
@section('title', 'Contact Us')
@section('description', 'Contact Ashfi Collection by phone, WhatsApp or email for help with sizing, orders and styling.')
@section('banner')
  @include('partials.page-banner', ['crumbs' => [['Contact', null]], 'heading' => 'Contact Us', 'lead' => 'We\'d love to help with sizing, orders and styling.'])
@endsection
@section('content')
@php $b = setting('brand'); $tel = preg_replace('/[^+\d]/', '', $b['phone'] ?? ''); @endphp
<main id="main" class="page-body">
  <div class="container">
    <div class="contact-grid">
      <div>
        <h2>Get in Touch</h2>
        <div class="contact-cards" id="contactCards">
          <div class="contact-card"><x-ui.icon name="phone" :size="22" /><div><h3>Call us</h3><p><a href="tel:{{ $tel }}">{{ $b['phone'] ?? '' }}</a></p></div></div>
          <div class="contact-card"><x-ui.icon name="chat" :size="22" /><div><h3>WhatsApp</h3><p><a href="https://wa.me/{{ $b['whatsapp'] ?? '' }}" target="_blank" rel="noopener">Chat with us</a></p></div></div>
          <div class="contact-card"><x-ui.icon name="mail" :size="22" /><div><h3>Email</h3><p><a href="mailto:{{ $b['email'] ?? '' }}">{{ $b['email'] ?? '' }}</a></p></div></div>
          <div class="contact-card"><x-ui.icon name="pin" :size="22" /><div><h3>Visit</h3><p>{{ $b['address'] ?? '' }}</p></div></div>
          <div class="contact-card"><x-ui.icon name="clock" :size="22" /><div><h3>Opening hours</h3><p>{{ $b['hours'] ?? '' }}</p></div></div>
          <div class="contact-card"><x-ui.icon name="facebook" :size="22" /><div><h3>Follow us</h3><p><a href="{{ $b['facebook'] ?? '#' }}" target="_blank" rel="noopener">Facebook</a> · <a href="{{ $b['instagram'] ?? '#' }}" target="_blank" rel="noopener">Instagram</a></p></div></div>
        </div>
      </div>
      <div class="panel" style="margin:0">
        <h2>Send a Message</h2>
        <form id="contactForm" class="form-grid" method="post" action="{{ route('contact.send') }}" data-ajax="message" data-msg="#contactMsg" data-reset novalidate>@csrf
          <div hidden aria-hidden="true"><label>Leave this empty <input name="website" tabindex="-1" autocomplete="off"></label></div>
          @include('partials.field', ['id' => 'cName', 'name' => 'name', 'label' => 'Your name', 'attrs' => 'autocomplete="name" required'])
          @include('partials.field', ['id' => 'cPhone', 'name' => 'phone', 'label' => 'Phone number', 'type' => 'tel', 'attrs' => 'inputmode="tel" autocomplete="tel" placeholder="01XXXXXXXXX" required'])
          @include('partials.field', ['id' => 'cMsg', 'name' => 'message', 'label' => 'Message', 'textarea' => true, 'attrs' => 'required'])
          <button type="submit" class="btn btn--primary">Send Message</button>
          <p class="news-msg" id="contactMsg" role="status" style="color:var(--burgundy)"></p>
        </form>
      </div>
    </div>
    <section class="faq__more" id="faq" style="margin-top:64px" aria-labelledby="faqTitle"><h2 id="faqTitle">Looking for a quick answer?</h2><p>Delivery, payment, sizing and exchanges are all covered in our FAQs.</p><a class="btn btn--primary" href="{{ route('faq') }}">Read the FAQs</a></section>
  </div>
</main>
@endsection
