# Ashfi Collection – Static Storefront

Plain HTML, CSS and JavaScript. No build step, no backend, no dependencies.
Open `index.html` through any static server (or just double-click it) to run.

```
index.html  shop.html  product.html  cart.html  checkout.html  order-success.html
wishlist.html  offers.html  track-order.html  size-guide.html  faq.html
about.html  contact.html  policy.html  terms.html  404.html
login.html  register.html  forgot-password.html  reset-password.html
account.html  account-orders.html  account-order.html  account-addresses.html  account-profile.html
sitemap.xml  robots.txt  site.webmanifest
css/style.css          all styling (brand colours are CSS variables at the top)
js/data.js             ALL content: brand info, hero slides, categories, products, reviews, delivery charges
js/store.js            cart, wishlist, coupons, orders and customer accounts (localStorage)
js/components.js       header, footer, drawer, search, bottom nav, product card, quick view, toasts
js/hero.js             homepage slider
js/<page>.js           page-specific logic
images/                openly licensed stand-in photos (JPG) + favicon + OG cover
images/hero/           slider images (lg, md, sm WebP) and demo video
images/brand/          logos and Facebook graphics
```

## Brand assets
Everything is in `images/brand/`. Logo lettering is converted to shapes, so no fonts are needed to open the files.

| File | Use |
| --- | --- |
| `logo-horizontal.svg` / `.png` | Website header, invoices, light backgrounds (woman-in-saree badge beside the name) |
| `logo-horizontal-light.svg` / `.png` | Footer and dark backgrounds |
| `logo-stacked.svg` / `.png` (and `-light`) | Full logo with tagline: packaging, tags, square layouts |
| `monogram.svg` / `.png` | Watermarks, stamps, stickers |
| `facebook-profile.png` | Facebook profile picture, 1080×1080. Stays inside Facebook's circle crop |
| `facebook-cover.png` | Facebook cover, 1640×924. Key content sits in the central 1640×624 band so it survives desktop cropping |

Colours: burgundy `#722F45`, cream `#F3EBDD`, gold `#C5A46D`, dark gold `#9A7B45`. Fonts: Playfair Display and Inter, with Pinyon Script for the tagline.

## Before launch – replace these placeholders
0. **Backend (required for real orders)** – accounts, orders and coupons run only in the visitor's browser. Orders never reach you, and anything in the browser can be edited, so logins, order placement, prices, coupon checks and password-reset emails must move to the server. All of it is in `js/store.js`.
1. **Product photos** – edit `images` in `js/data.js` (3:4 JPG/WebP, ~900×1200). The files in `images/products`, `images/categories`, `images/hero` and `images/story.jpg` are Wikimedia Commons photos of similar garments, not your stock. Each is credited in `photoCredits` (`js/data.js`), shown on `policy.html#credits`; keep that list in sync as you swap in your own photos.
2. **Domain** – `brand.url` in `js/data.js`, plus every page's canonical and `og:` tags, `sitemap.xml` and `robots.txt` use `https://ashficollection.com`. Replace with the real domain. Facebook needs `images/og-cover.png` (included) at that absolute URL.
3. **Contact details, Facebook and Instagram links** – `brand` in `js/data.js`.
4. **Testimonials, "About" statistics, policy text, terms and size chart** – sample/demo content.
   Demo coupons are in `coupons` in `js/data.js`.
5. **Delivery charges and free-delivery threshold** – `delivery` in `js/data.js`.

## Editing products
Add or change objects in `products` (`js/data.js`). `salePrice` is optional; set `isNew` / `isBestSeller` for badges.
Homepage "Featured Products" is controlled by `featuredIds`.

## Homepage slider
Slides live in `heroSlides` (`js/data.js`), and `js/hero.js` adds any slide that isn't already in the page. Slide 1 is also written in `index.html`, so keep the two in step.
- **Images:** three crops per slide, `lg` 1920×1000, `md` 1200×900, `sm` 750×1100. Keep the subject clear of the text side.
- **Video:** add `video: { lg, sm }` to a slide. Use short, silent, looping MP4s (H.264) under about 2 MB. Video is skipped for visitors on Data Saver, slow connections or reduced motion, who see the image instead.
- `images/hero/slide-1-*.mp4` is a demo made by panning across a fabric photo. Replace it with real footage.

## Laravel integration notes
- Slider: loop your slides in Blade using the `.hero-slide` markup from `index.html`, with `h1` on the first slide and `h2` on the rest, and drop `heroSlides`. Slide behaviour comes from the `data-*` attributes, and `js/hero.js` only adds slides the page doesn't already have. Or return the same shape from `/api/hero-slides`.
- Replace `window.ASHFI.products` with a `fetch('/api/products')` result (same shape) and call the page init after it resolves.
- `AshfiCheckout.getOrderPayload()` (in `js/checkout.js`) returns the order JSON ready to `POST` to `/api/orders`; the send step is marked with a `TODO`.
- For better SEO, render `product.html` and `shop.html` on the server (Blade) with clean URLs such as `/products/banarasi-katan-saree`, then update `sitemap.xml`.
- Header/footer are rendered by `js/components.js`; in Blade, move them into partials.
- Cart and wishlist storage lives only in `js/store.js`, so swapping to server sessions is a single-file change.

## Notes
- Fonts (Playfair Display, Inter, Hind Siliguri, Pinyon Script) load from Google Fonts; the site falls back to system serif/sans if offline.
- Colour options on a product are selectable but share one gallery (per-colour photos can be added later).
