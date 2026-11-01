# Ashfi Collection – Static Storefront

Plain HTML, CSS and JavaScript. No build step, no backend, no dependencies.
Open `index.html` through any static server (or just double-click it) to run.

```
index.html  shop.html  product.html  cart.html  checkout.html
wishlist.html  about.html  contact.html  policy.html  404.html
css/style.css          all styling (brand colours are CSS variables at the top)
js/data.js             ALL content: brand info, categories, products, reviews, delivery charges
js/store.js            cart + wishlist (localStorage), money/discount helpers
js/components.js       header, footer, drawer, search, bottom nav, product card, quick view, toasts
js/<page>.js           page-specific logic
images/                openly licensed stand-in photos (JPG) + favicon + OG cover
```

## Before launch – replace these placeholders
1. **Product photos** – edit `images` in `js/data.js` (3:4 JPG/WebP, ~900×1200). The files in `images/products`, `images/categories`, `images/hero.jpg` and `images/story.jpg` are Wikimedia Commons photos of similar garments, not your stock. Each is credited in `photoCredits` (`js/data.js`), shown on `policy.html#credits`; keep that list in sync as you swap in your own photos.
2. **Domain** – every page's `<meta property="og:image">` / `og:url` uses `https://ashficollection.com`. Replace with the real domain. Facebook needs `images/og-cover.png` (included) at that absolute URL.
3. **Contact details, Facebook and Instagram links** – `brand` in `js/data.js`.
4. **Testimonials, "About" statistics and policy text** – sample/demo content.
5. **Delivery charges and free-delivery threshold** – `delivery` in `js/data.js`.

## Editing products
Add or change objects in `products` (`js/data.js`). `salePrice` is optional; set `isNew` / `isBestSeller` for badges.
Homepage "Featured Products" is controlled by `featuredIds`.

## Laravel integration notes
- Replace `window.ASHFI.products` with a `fetch('/api/products')` result (same shape) and call the page init after it resolves.
- `AshfiCheckout.getOrderPayload()` (in `js/checkout.js`) returns the order JSON ready to `POST` to `/api/orders`; the send step is marked with a `TODO`.
- Header/footer are rendered by `js/components.js`; in Blade, move them into partials.
- Cart and wishlist storage lives only in `js/store.js`, so swapping to server sessions is a single-file change.

## Notes
- Fonts (Playfair Display, Inter, Hind Siliguri, Pinyon Script) load from Google Fonts; the site falls back to system serif/sans if offline.
- Colour options on a product are selectable but share one gallery (per-colour photos can be added later).
