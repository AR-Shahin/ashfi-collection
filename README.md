# Ashfi Collection – Laravel Storefront

Women's fashion store for Bangladesh (kurti, two piece, three piece, saree, orna).
Laravel 13 with Blade pages, an SQLite database by default, and a [Filament](https://filamentphp.com) admin panel at `/admin`.
There is no front-end build step: CSS and JS are plain files in `public/`.

## Run it locally

Requirements: PHP 8.3+ with `pdo_sqlite`, and Composer.

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

- Shop: http://127.0.0.1:8000
- Admin: http://127.0.0.1:8000/admin. The seeder creates `admin@ashficollection.com` with the password `password`. Set `ADMIN_EMAIL` and `ADMIN_PASSWORD` in `.env` **before** seeding a live site, or change the password straight after.

Run the tests with `php artisan test`.

## What lives where

```
routes/web.php                       every URL (old *.html links redirect to the new ones)
app/Http/Controllers/                storefront pages, cart, checkout, tracking, auth, my account
app/Services/Cart.php                cart, coupons, delivery charges and totals – all money is worked out here
app/Services/Wishlist.php            wishlist (session for guests, database once logged in; merged at login)
app/Models/                          Product, Category, HeroSlide, Coupon, Order, OrderItem, Address, Faq, Page, Setting…
app/Filament/                        admin panel: resources, Settings page, dashboard widgets
resources/views/layouts/app.blade.php   <head>, SEO tags, header, footer, drawer, bottom nav
resources/views/pages/               storefront pages
resources/views/account/, auth/      customer area and login/register/password reset
resources/views/components/          product card, price, options, quantity, icon
config/store.php                     districts, photo credits, fallback brand/delivery values
database/seeders/                    imports the original catalogue (data/storefront.json), FAQs and page text
public/css/style.css                 all storefront styling (brand colours are CSS variables at the top)
public/js/app.js                     drawer, live search, quick view, toasts, fetch-based cart/wishlist/coupon forms
public/js/hero.js                    homepage slider (reads the slides Blade renders)
```

Every form works without JavaScript. `app.js` only sends the same forms with `fetch` so the page doesn't reload.

## Managing the store (admin)

| Section | What you can do |
| --- | --- |
| **Orders** | See new orders (badge = waiting for confirmation), mark them Confirmed → Shipped → Delivered or Cancelled. Customers see each step on *My Orders* and *Track Order*. |
| **Coupons** | Percent (with optional cap), flat amount or free delivery; minimum spend and expiry date. |
| **Customers** | Accounts, order counts, and which users are admins. |
| **Products** | Photos (drag to reorder; first is the main image), price and sale price, sizes, colours, details, New / Best seller / Featured flags. |
| **Categories** | Name, blurb, tile image and order (drag rows). |
| **Home slider** | Motto, title, text, two buttons, desktop/tablet/phone images, optional video, transition and timing. |
| **Reviews, FAQs, Pages** | Home-page testimonials, the FAQ page, and the Size Guide, Terms and Policies text. |
| **Settings** | Store name, contact details, social links, announcement-bar messages, delivery charges and the free-delivery threshold. |
| **Messages, Newsletter** | Contact-form messages and newsletter sign-ups. |

Uploaded images are stored in `public/uploads/` (git-ignored). Back this folder up together with the database.

## Customer features

- Shop with category, price, sale/new/best-seller filters, search and sorting (all in the URL, so filtered pages can be shared).
- Product pages with gallery, colour and size choice, quantity, Buy Now, and related products. Quick view from any product card.
- Server-side cart and coupons. Delivery is ৳70 inside Dhaka and ৳130 outside, free over ৳5,000 (change these in Settings). Payment is Cash on Delivery.
- Guest checkout, or log in to reuse saved addresses.
- Order confirmation, and tracking by order number **plus** the phone number used for the order.
- Accounts: dashboard, orders (with cancel while still "Placed" and order again), address book, profile and password, wishlist.
- Password reset by email.

## Before going live

1. **Email.** `MAIL_MAILER=log` writes password-reset emails to `storage/logs/laravel.log`. Set real SMTP details in `.env` (`MAIL_*`) so customers receive them.
2. **Domain.** Set `APP_URL`, `APP_ENV=production` and `APP_DEBUG=false`. Canonical tags, the sitemap (`/sitemap.xml`) and `robots.txt` (`/robots.txt`) use `APP_URL` automatically. Also update the website field in admin → Settings.
3. **Admin password.** Change it (admin → Customers → your account).
4. **Content.** Product photos in `public/images/` are openly licensed Wikimedia Commons stand-ins, credited on `/policy#credits` (list in `config/store.php`). Replace them with your own photos and trim the credits list. Reviews, About-page figures, policy, terms and the size chart are sample text you can edit in the admin.
5. **Database.** SQLite is fine for a small shop. For MySQL, change the `DB_*` values in `.env` and run `php artisan migrate --seed`.
6. **Performance.** Run `php artisan optimize` after deploying.

## Brand assets

Logos and social graphics are in `public/images/brand/` and `public/graphic-design/`. The site header uses `graphic-design/logo-horizontal-light.svg` on the burgundy header.

| File | Use |
| --- | --- |
| `logo-horizontal.svg` / `.png` | Light backgrounds, invoices, admin panel |
| `logo-horizontal-light.svg` / `.png` | Header, footer and dark backgrounds |
| `logo-stacked.svg` / `.png` (and `-light`) | Full logo with tagline: packaging, tags, square layouts |
| `monogram.svg` / `.png` | Watermarks, stamps, stickers |
| `facebook-profile.png` | Facebook profile picture, 1080×1080 |
| `facebook-cover.png` | Facebook cover, 1640×924 |

Colours: burgundy `#722F45`, cream `#F3EBDD`, gold `#C5A46D`, dark gold `#9A7B45`. Fonts: Playfair Display and Inter, with Pinyon Script for the tagline.
