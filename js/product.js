/* Product details page – driven by ?id=<product-id>. */
(function () {
  "use strict";
  var S = window.Store, D = S.data, U = window.UI, esc = S.escape;

  function setMeta(p) {
    var cat = S.getCategory(p.category);
    document.title = p.name + " | Ashfi Collection";
    var set = function (sel, val) { var el = document.querySelector(sel); if (el) el.setAttribute("content", val); };
    var desc = p.short + " Available in " + p.sizes.join(", ") + ". Price " + S.money(S.unitPrice(p)) + ". Nationwide delivery in Bangladesh.";
    set('meta[name="description"]', desc);
    set('meta[property="og:title"]', document.title);
    set('meta[property="og:description"]', desc);
    var og = document.querySelector('meta[property="og:image"]');
    if (og) og.setAttribute("content", og.getAttribute("content").replace(/\/images\/og-cover\.png$/, "/" + p.images[0]));
    var ogurl = document.querySelector('meta[property="og:url"]');
    if (ogurl) ogurl.setAttribute("content", ogurl.getAttribute("content").replace(/product\.html.*$/, "product.html?id=" + p.id));
    U.setCanonical("product.html?id=" + encodeURIComponent(p.id));
    var base = D.brand.url + "/", url = base + "product.html?id=" + encodeURIComponent(p.id);
    U.jsonLd({
      "@context": "https://schema.org", "@type": "Product", name: p.name, sku: p.id, description: p.description,
      image: p.images.map(function (src) { return base + src; }), category: cat ? cat.name : undefined,
      brand: { "@type": "Brand", name: D.brand.name },
      offers: { "@type": "Offer", url: url, price: S.unitPrice(p), priceCurrency: "BDT", availability: "https://schema.org/InStock", itemCondition: "https://schema.org/NewCondition" }
    });
    U.jsonLd({
      "@context": "https://schema.org", "@type": "BreadcrumbList", itemListElement: [
        { "@type": "ListItem", position: 1, name: "Home", item: base },
        { "@type": "ListItem", position: 2, name: "Shop", item: base + "shop.html" },
        { "@type": "ListItem", position: 3, name: cat ? cat.name : "Products", item: base + "shop.html?category=" + p.category },
        { "@type": "ListItem", position: 4, name: p.name, item: url }
      ]
    });
    document.getElementById("pdpCrumbs").innerHTML =
      '<li><a href="index.html">Home</a></li><li><a href="shop.html">Shop</a></li>' +
      '<li><a href="shop.html?category=' + p.category + '">' + esc(cat ? cat.name : "") + "</a></li>" +
      '<li aria-current="page">' + esc(p.name) + "</li>";
  }

  function render(p) {
    var thumbs = p.images.map(function (src, i) {
      return '<button type="button" class="thumb" data-i="' + i + '" aria-label="Show image ' + (i + 1) + " of " + p.images.length + '"' + (i === 0 ? ' aria-current="true"' : "") + '>' +
        '<span class="img-wrap"><img src="' + src + '" alt="" width="600" height="800" loading="lazy" decoding="async"></span></button>';
    }).join("");
    var badges = (p.isBestSeller ? '<span class="badge badge--best">Best Seller</span>' : "") + (p.isNew ? '<span class="badge badge--new">New Arrival</span>' : "");
    var details = Object.keys(p.details).map(function (k) { return "<dt>" + esc(k) + "</dt><dd>" + esc(p.details[k]) + "</dd>"; }).join("");
    var cat = S.getCategory(p.category);

    document.getElementById("pdp").innerHTML =
      '<div class="gallery"><div class="gallery__main"><span class="img-wrap" id="mainWrap"><img id="mainImg" src="' + p.images[0] + '" alt="' + esc(p.name) + '" width="600" height="800" fetchpriority="high"></span>' +
        '<button type="button" class="gallery__nav gallery__nav--prev" data-nav="-1" aria-label="Previous image">' + U.icon("left", 20) + "</button>" +
        '<button type="button" class="gallery__nav gallery__nav--next" data-nav="1" aria-label="Next image">' + U.icon("right", 20) + "</button></div>" +
        '<div class="gallery__thumbs">' + thumbs + "</div></div>" +
      '<div class="pdp__info" id="pdpInfo">' +
        '<p class="eyebrow">' + esc(cat ? cat.name : "") + "</p>" +
        (badges ? '<div class="pdp__badges">' + badges + "</div>" : "") +
        "<h1>" + esc(p.name) + "</h1>" + U.priceBlock(p, "price--lg") +
        '<p class="pdp__short">' + esc(p.description) + "</p>" +
        U.optionsMarkup(p, "pdp") +
        (p.sizes.length > 1 ? '<p class="pdp__sizeguide"><a class="link-arrow" href="size-guide.html">Size guide ' + U.icon("right", 16) + "</a></p>" : "") +
        '<div class="opt"><span class="opt" style="display:block;margin:0 0 10px;font-size:.8125rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)">Quantity</span>' + U.qtyMarkup("pdpQty") + "</div>" +
        '<div class="pdp__buy"><button type="button" class="btn btn--primary" id="pdpAdd">Add to Cart</button>' +
          '<button type="button" class="btn btn--outline" id="pdpBuy">Buy Now</button></div>' +
        '<button type="button" class="btn btn--outline btn--sm pdp__wish" data-action="wishlist" data-id="' + p.id + '" aria-pressed="false">' + U.icon("heart", 18) + ' <span class="js-wish-label">Wishlist</span></button>' +
        '<ul class="assure">' +
          "<li>" + U.icon("truck", 22) + "<div><strong>Nationwide delivery</strong><span>Inside Dhaka 1–2 days (" + S.money(D.delivery.insideDhaka) + "), outside Dhaka 3–5 days (" + S.money(D.delivery.outsideDhaka) + "). Free over " + S.money(D.delivery.freeOver) + ".</span></div></li>" +
          "<li>" + U.icon("cash", 22) + "<div><strong>Cash on Delivery</strong><span>Pay in cash when your order arrives.</span></div></li>" +
          "<li>" + U.icon("rotate", 22) + "<div><strong>7-day easy exchange</strong><span>Unworn items with tags can be exchanged.</span></div></li></ul>" +
        '<div class="acc">' +
          '<details open><summary>Product Details</summary><div class="acc__body"><dl class="spec">' + details + "</dl></div></details>" +
          '<details><summary>Delivery &amp; Returns</summary><div class="acc__body"><p>Orders are confirmed by phone and dispatched within 1–2 working days. See our <a class="link-arrow" href="policy.html#delivery">delivery</a> and <a class="link-arrow" href="policy.html#returns">return</a> policies.</p></div></details>' +
          '<details><summary>Care Instructions</summary><div class="acc__body"><p>' + esc(p.details.Care || "Follow the care label.") + ". Colours may appear slightly different on screen than in person.</p></div></details>" +
        "</div></div>";

    var info = document.getElementById("pdpInfo");
    U.bindOptions(info);
    U.hydrate(document.getElementById("pdp"));

    // gallery
    var idx = 0, main = document.getElementById("mainImg");
    function show(i) {
      idx = (i + p.images.length) % p.images.length;
      document.getElementById("mainWrap").classList.remove("is-loaded");
      main.src = p.images[idx];
      U.hydrate(document.getElementById("mainWrap").parentNode);
      Array.prototype.forEach.call(document.querySelectorAll(".thumb"), function (t, n) {
        if (n === idx) t.setAttribute("aria-current", "true"); else t.removeAttribute("aria-current");
      });
    }
    document.querySelector(".gallery").addEventListener("click", function (e) {
      var t = e.target.closest(".thumb"), n = e.target.closest("[data-nav]");
      if (t) show(parseInt(t.getAttribute("data-i"), 10));
      if (n) show(idx + parseInt(n.getAttribute("data-nav"), 10));
    });

    document.getElementById("pdpAdd").addEventListener("click", function () {
      var sel = U.readSelection(info, "pdp"); if (!sel) return;
      S.addToCart(p.id, sel);
      U.toast("Added to your cart", { href: "cart.html", linkText: "View cart" });
    });
    document.getElementById("pdpBuy").addEventListener("click", function () {
      var sel = U.readSelection(info, "pdp"); if (!sel) return;
      S.addToCart(p.id, sel);
      location.href = "checkout.html";
    });

    // related
    var related = D.products.filter(function (x) { return x.id !== p.id && x.category === p.category; });
    D.products.forEach(function (x) { if (related.length < 4 && x.id !== p.id && related.indexOf(x) === -1) related.push(x); });
    U.renderProducts(document.getElementById("relGrid"), related.slice(0, 4));
    document.getElementById("relSection").hidden = false;
  }

  function init() {
    var id = new URLSearchParams(location.search).get("id");
    var p = id && S.getProduct(id);
    if (!p) { location.replace("404.html"); return; }
    setMeta(p); render(p);
  }
  document.addEventListener("ashfi:ready", init);
})();
