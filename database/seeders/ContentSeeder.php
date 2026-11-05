<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Page;
use Illuminate\Database\Seeder;

/** FAQs and the long-form pages (size guide, terms, returns & privacy) editable in the admin. */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            'Orders & Delivery' => [
                ['How long does delivery take?', 'Inside Dhaka usually 1–2 working days, and 3–5 working days for the rest of Bangladesh.'],
                ['How much is delivery?', '৳70 inside Dhaka and ৳130 outside Dhaka. Orders over ৳5,000 are delivered free.'],
                ['How do I track my order?', 'Use our <a class="text-link" href="/track-order">Track Order</a> page with your order number and mobile number. If you have an account, your orders are also listed under <a class="text-link" href="/account/orders">My Orders</a>.'],
                ['Can I cancel my order?', 'Yes, while it has not been confirmed yet. Open the order in your account and choose Cancel Order, or contact us as soon as you can.'],
            ],
            'Payment' => [
                ['Do you offer Cash on Delivery?', 'Yes. You can pay in cash when your order arrives, anywhere in Bangladesh.'],
                ['Do you accept bKash, Nagad or cards?', 'Online payment is coming soon. For now, all orders are Cash on Delivery.'],
                ['How do I use a coupon code?', 'Enter the code in your cart or at checkout and choose Apply. One coupon can be used per order. See current codes on our <a class="text-link" href="/offers">Offers</a> page.'],
            ],
            'Sizes & Returns' => [
                ['How do I choose my size?', 'Check our <a class="text-link" href="/size-guide">Size Guide</a>. If you are between sizes or unsure, message us with your measurements and we will help you choose.'],
                ['Can I exchange a product?', 'Yes, within 7 days of receiving it. See our <a class="text-link" href="/policy#returns">Return &amp; Exchange policy</a> for the full conditions.'],
            ],
            'Your Account' => [
                ['Do I need an account to order?', 'No. You can check out as a guest. An account lets you see past orders and save addresses.'],
                ['I forgot my password. What do I do?', 'Use the <a class="text-link" href="/forgot-password">Forgot Password</a> page and we will email you a link to choose a new one.'],
            ],
        ];
        $n = 0;
        foreach ($faqs as $group => $items) {
            foreach ($items as [$q, $a]) {
                Faq::updateOrCreate(['question' => $q], ['group' => $group, 'answer' => $a, 'sort_order' => $n++, 'is_active' => true]);
            }
        }

        Page::updateOrCreate(['slug' => 'size-guide'], [
            'title' => 'Size Guide',
            'lead' => 'Find your perfect fit.',
            'meta_description' => 'Ashfi Collection size guide: how to measure and body measurements for kurti, two piece, three piece, saree and orna.',
            'body' => <<<'HTML'
<p class="notice" style="margin-bottom:28px">These are sample measurements. Please replace them with your actual garment measurements before launch.</p>
<h2>How to measure</h2>
<ul>
  <li><strong>Chest:</strong> measure around the fullest part of your chest, keeping the tape level.</li>
  <li><strong>Waist:</strong> measure around the narrowest part of your waist.</li>
  <li><strong>Hip:</strong> measure around the fullest part of your hips.</li>
  <li><strong>Length:</strong> measure from the highest point of the shoulder down to where you want the hem.</li>
</ul>
<h2>Kurti, two piece and three piece (body size, inches)</h2>
<div class="table-wrap" tabindex="0" role="region" aria-label="Size chart in inches"><table><thead><tr><th scope="col">Size</th><th scope="col">Chest</th><th scope="col">Waist</th><th scope="col">Hip</th><th scope="col">Top length</th></tr></thead><tbody><tr><th scope="row">S</th><td>34</td><td>28</td><td>37</td><td>38</td></tr><tr><th scope="row">M</th><td>36</td><td>30</td><td>39</td><td>39</td></tr><tr><th scope="row">L</th><td>38</td><td>32</td><td>41</td><td>40</td></tr><tr><th scope="row">XL</th><td>40</td><td>34</td><td>43</td><td>41</td></tr><tr><th scope="row">XXL</th><td>42</td><td>36</td><td>45</td><td>42</td></tr></tbody></table></div>
<h2>Saree and orna</h2>
<div class="table-wrap" tabindex="0" role="region" aria-label="Saree and orna lengths"><table><thead><tr><th scope="col">Item</th><th scope="col">Size</th><th scope="col">Length</th></tr></thead><tbody><tr><th scope="row">Saree</th><td>Free Size</td><td>6.2 to 6.3 metres including blouse piece</td></tr><tr><th scope="row">Orna</th><td>Free Size</td><td>2.3 to 2.4 metres</td></tr></tbody></table></div>
<h2>Between two sizes?</h2>
<p>Choose the larger size for a relaxed fit, or <a class="text-link" href="/contact">message us</a> with your measurements and we will help. Unworn items can be exchanged within 7 days under our <a class="text-link" href="/policy#returns">Return &amp; Exchange policy</a>.</p>
HTML,
        ]);

        Page::updateOrCreate(['slug' => 'terms'], [
            'title' => 'Terms & Conditions',
            'lead' => 'The terms for using our website and placing orders.',
            'meta_description' => 'Terms and conditions for shopping with Ashfi Collection: orders, prices, coupons, delivery and your account.',
            'body' => <<<'HTML'
<p class="notice" style="margin-bottom:28px">These terms are sample text. Please have them reviewed and adapted to your actual business before launch.</p>
<h2>Using this website</h2>
<p>By using this website or placing an order, you agree to these terms. You must give accurate details when you order or create an account.</p>
<h2>Orders</h2>
<p>Orders are confirmed by phone before dispatch. We may decline or cancel an order if an item is unavailable, if we cannot reach you, or if details appear incorrect. You can cancel an order until it has been confirmed.</p>
<h2>Prices and payment</h2>
<p>All prices are in Bangladeshi Taka (৳). Delivery charges are shown at checkout. Payment is Cash on Delivery unless another method is offered at checkout.</p>
<h2>Coupons</h2>
<ul><li>One coupon can be used per order.</li><li>Coupons may have a minimum spend, a maximum discount and an expiry date.</li><li>Coupons have no cash value and cannot be applied to an order after it is placed.</li></ul>
<h2>Product colours and sizes</h2>
<p>We show our products as accurately as we can, but colours can look different on different screens and measurements can vary slightly.</p>
<h2>Delivery, returns and exchanges</h2>
<p>Our <a class="text-link" href="/policy#delivery">Delivery Policy</a> and <a class="text-link" href="/policy#returns">Return &amp; Exchange policy</a> form part of these terms.</p>
<h2>Your account</h2>
<p>You are responsible for keeping your password private and for activity on your account. Tell us immediately if you think someone else has used it.</p>
<h2>Intellectual property</h2>
<p>The Ashfi Collection name, logo and website content may not be used without permission. Some photos are used under the open licences listed in our <a class="text-link" href="/policy#credits">Photo Credits</a>.</p>
<h2>Governing law</h2>
<p>These terms are governed by the laws of Bangladesh.</p>
<h2>Contact</h2>
<p>Questions? Visit our <a class="text-link" href="/contact">Contact page</a>.</p>
HTML,
        ]);

        Page::updateOrCreate(['slug' => 'policy'], [
            'title' => 'Store Policies',
            'lead' => 'Delivery, returns, privacy and photo credits.',
            'meta_description' => 'Ashfi Collection delivery charges and times, return and exchange policy, privacy policy and photo credits.',
            // Delivery table and photo credits are generated from settings; this body holds returns + privacy.
            'body' => <<<'HTML'
<h2 id="returns">Return &amp; Exchange</h2>
<ul>
  <li>Exchange requests must be made within 7 days of receiving the product.</li>
  <li>Items must be unworn, unwashed and in their original packaging with tags attached.</li>
  <li>If you receive a damaged or wrong item, contact us within 24 hours with photos and we will arrange a replacement.</li>
  <li>Delivery charges for change-of-mind exchanges are paid by the customer.</li>
</ul>
<h2 id="privacy">Privacy Policy</h2>
<p>We respect your privacy. We only collect the details needed to process and deliver your order and to run your account: your name, phone number, email, delivery address and order history. Passwords are stored only as secure hashes.</p>
<p>We use a session cookie to keep your cart and sign-in working. We never sell your information to third parties, and we share your name, phone and address only with the courier delivering your order. To ask for a copy of your data or to have your account deleted, <a class="text-link" href="/contact">contact us</a>.</p>
HTML,
        ]);
    }
}
