/**
 * Ashfi Collection – shared UI components.
 * Renders the announcement bar, header, mobile drawer, footer and bottom nav
 * into placeholder elements, and provides reusable pieces (product card,
 * quick view, toast, option pickers) used by every page.
 */
(function () {
  "use strict";
  var S = window.Store;
  var D = S.data;
  var esc = S.escape;

  /* ---------------------------------------------------------------- icons */
  var ICONS = {
    search: '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
    bag: '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>',
    heart: '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>',
    menu: '<line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>',
    x: '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
    truck: '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
    eye: '<path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/>',
    star: '<path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/>',
    right: '<path d="m9 18 6-6-6-6"/>',
    left: '<path d="m15 18-6-6 6-6"/>',
    down: '<path d="m6 9 6 6 6-6"/>',
    plus: '<path d="M5 12h14"/><path d="M12 5v14"/>',
    minus: '<path d="M5 12h14"/>',
    trash: '<path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>',
    up: '<path d="m5 12 7-7 7 7"/><path d="M12 19V5"/>',
    home: '<path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
    grid: '<rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>',
    phone: '<path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/>',
    mail: '<path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/><rect x="2" y="4" width="20" height="16" rx="2"/>',
    pin: '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>',
    facebook: '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
    instagram: '<rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>',
    check: '<path d="M20 6 9 17l-5-5"/>',
    sliders: '<line x1="21" x2="14" y1="4" y2="4"/><line x1="10" x2="3" y1="4" y2="4"/><line x1="21" x2="12" y1="12" y2="12"/><line x1="8" x2="3" y1="12" y2="12"/><line x1="21" x2="16" y1="20" y2="20"/><line x1="12" x2="3" y1="20" y2="20"/><line x1="14" x2="14" y1="2" y2="6"/><line x1="8" x2="8" y1="10" y2="14"/><line x1="16" x2="16" y1="18" y2="22"/>',
    shield: '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
    rotate: '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>',
    cash: '<rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>',
    chat: '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>',
    clock: '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
    info: '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>',
    user: '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
    logout: '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
    box: '<path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
    tag: '<path d="M12.59 2.59A2 2 0 0 0 11.17 2H4a2 2 0 0 0-2 2v7.17a2 2 0 0 0 .59 1.42l8.7 8.7a2.43 2.43 0 0 0 3.42 0l6.58-6.58a2.43 2.43 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5"/>',
    lock: '<rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
    edit: '<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
    copy: '<rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2"/>'
  };

  function icon(name, size, cls) {
    size = size || 20;
    return '<svg class="icon ' + (cls || "") + '" width="' + size + '" height="' + size + '" viewBox="0 0 24 24" fill="none" ' +
      'stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' +
      (ICONS[name] || "") + "</svg>";
  }

  function stars(n) {
    var out = '<span class="stars" role="img" aria-label="' + n + ' out of 5 stars">';
    for (var i = 1; i <= 5; i++) out += icon("star", 16, i <= n ? "is-on" : "");
    return out + "</span>";
  }

  /* ---------------------------------------------------------------- toast */
  function toast(message, opts) {
    opts = opts || {};
    var box = document.getElementById("toasts");
    if (!box) return;
    var el = document.createElement("div");
    el.className = "toast" + (opts.type ? " toast--" + opts.type : "");
    el.innerHTML = '<span class="toast__icon">' + icon(opts.type === "error" ? "info" : "check", 18) + "</span>" +
      "<span>" + esc(message) + "</span>" +
      (opts.href ? '<a class="toast__link" href="' + opts.href + '">' + esc(opts.linkText || "View") + "</a>" : "");
    box.appendChild(el);
    requestAnimationFrame(function () { el.classList.add("is-in"); });
    setTimeout(function () {
      el.classList.remove("is-in");
      setTimeout(function () { el.remove(); }, 300);
    }, opts.duration || 3200);
  }

  /* ---------------------------------------------------------------- forms */
  /** Shows or clears the message in the #<id>Err element that belongs to a field. */
  function fieldError(field, msg) {
    var el = document.getElementById(field.id + "Err");
    field.setAttribute("aria-invalid", msg ? "true" : "false");
    if (el) el.textContent = msg || "";
    return !msg;
  }

  function couponMarkup(t) {
    if (t.coupon) {
      return '<div class="coupon__on">' + icon("tag", 18) + "<span><strong>" + esc(t.coupon.code) + "</strong> " + esc(t.coupon.title) + "</span>" +
        '<button type="button" class="link-btn" data-coupon-remove aria-label="Remove coupon ' + esc(t.coupon.code) + '">Remove</button></div>' +
        (t.couponMessage ? '<p class="coupon__msg is-error" role="status">' + esc(t.couponMessage) + "</p>" : "");
    }
    return '<form class="coupon" data-coupon-form novalidate><label class="sr-only" for="couponCode">Coupon code</label>' +
      '<input id="couponCode" name="code" placeholder="Coupon code" autocomplete="off" autocapitalize="characters" spellcheck="false">' +
      '<button type="submit" class="btn btn--outline btn--sm">Apply</button></form>' +
      '<p class="coupon__msg" role="status"></p><p class="coupon__link"><a href="offers.html">View available offers</a></p>';
  }

  /** root keeps working across re-renders because it listens from outside the coupon box. */
  function bindCoupon(root) {
    root.addEventListener("submit", function (e) {
      if (!e.target.matches("[data-coupon-form]")) return;
      e.preventDefault();
      var input = e.target.elements.code, msg = root.querySelector(".coupon__msg");
      if (!input.value.trim()) { msg.textContent = "Enter a coupon code."; msg.classList.add("is-error"); input.focus(); return; }
      var res = S.applyCoupon(input.value);
      if (res.ok) { toast(res.message); return; }
      msg.textContent = res.message; msg.classList.add("is-error");
    });
    root.addEventListener("click", function (e) {
      if (e.target.closest("[data-coupon-remove]")) { S.removeCoupon(); toast("Coupon removed"); }
    });
  }

  /* ------------------------------------------------------------------ SEO */
  function setCanonical(path) {
    var url = D.brand.url + "/" + path, link = document.querySelector('link[rel="canonical"]'), og = document.querySelector('meta[property="og:url"]');
    if (!link) { link = document.createElement("link"); link.rel = "canonical"; document.head.appendChild(link); }
    link.href = url;
    if (og) og.setAttribute("content", url);
  }

  function jsonLd(data) {
    var el = document.createElement("script");
    el.type = "application/ld+json";
    el.textContent = JSON.stringify(data).replace(/</g, "\\u003c");
    document.head.appendChild(el);
  }

  /* ----------------------------------------------------- image skeletons */
  function fillIcons(root) {
    Array.prototype.forEach.call((root || document).querySelectorAll("[data-ic]"), function (el) {
      el.outerHTML = icon(el.getAttribute("data-ic"), 20);
    });
  }

  function hydrate(root) {
    root = root || document;
    fillIcons(root);
    var wraps = root.querySelectorAll(".img-wrap");
    Array.prototype.forEach.call(wraps, function (w) {
      var img = w.querySelector("img");
      if (!img || w.classList.contains("is-loaded")) return;
      var done = function () { w.classList.add("is-loaded"); };
      if (img.complete && img.naturalWidth) done();
      else { img.addEventListener("load", done, { once: true }); img.addEventListener("error", done, { once: true }); }
    });
    syncWishlistButtons(root);
  }

  function syncWishlistButtons(root) {
    root = root || document;
    Array.prototype.forEach.call(root.querySelectorAll('[data-action="wishlist"]'), function (b) {
      var on = S.inWishlist(b.getAttribute("data-id"));
      b.setAttribute("aria-pressed", on ? "true" : "false");
      b.classList.toggle("is-active", on);
      var label = on ? "Remove from wishlist" : "Add to wishlist";
      if (b.classList.contains("btn")) { var t = b.querySelector(".js-wish-label"); if (t) t.textContent = on ? "Saved" : "Wishlist"; }
      else b.setAttribute("aria-label", label);
    });
  }

  /* --------------------------------------------------------- product card */
  function priceBlock(p, cls) {
    var pct = S.discountPct(p);
    if (!pct) return '<div class="price ' + (cls || "") + '"><span class="price__now">' + S.money(p.price) + "</span></div>";
    return '<div class="price ' + (cls || "") + '"><span class="price__now">' + S.money(p.salePrice) + "</span>" +
      '<s class="price__was"><span class="sr-only">Regular price </span>' + S.money(p.price) + "</s>" +
      '<span class="price__off">' + pct + "% off</span></div>";
  }

  function productCard(p, opts) {
    opts = opts || {};
    var cat = S.getCategory(p.category);
    var badges = "";
    if (p.isBestSeller) badges += '<span class="badge badge--best">Best Seller</span>';
    if (p.isNew) badges += '<span class="badge badge--new">New Arrival</span>';
    var href = "product.html?id=" + encodeURIComponent(p.id);
    return '<article class="product-card">' +
      '<div class="product-card__media">' +
        '<a class="product-card__img" href="' + href + '" tabindex="-1" aria-hidden="true">' +
          '<span class="img-wrap"><img src="' + p.images[0] + '" alt="' + esc(p.name) + '" width="600" height="800" loading="' + (opts.eager ? "eager" : "lazy") + '" decoding="async"></span>' +
        "</a>" +
        '<div class="badges">' + badges + "</div>" +
        '<div class="product-card__tools">' +
          '<button type="button" class="tool-btn" data-action="wishlist" data-id="' + p.id + '" aria-pressed="false" aria-label="Add to wishlist">' + icon("heart", 18) + "</button>" +
          '<button type="button" class="tool-btn" data-action="quick-view" data-id="' + p.id + '" aria-label="Quick view: ' + esc(p.name) + '">' + icon("eye", 18) + "</button>" +
        "</div>" +
      "</div>" +
      '<div class="product-card__body">' +
        '<p class="product-card__cat">' + esc(cat ? cat.name : "") + "</p>" +
        '<h3 class="product-card__name"><a href="' + href + '">' + esc(p.name) + "</a></h3>" +
        priceBlock(p) +
        '<button type="button" class="btn btn--primary btn--block btn--sm" data-action="add-to-cart" data-id="' + p.id + '">Add to Cart</button>' +
      "</div></article>";
  }

  function renderProducts(el, list, opts) {
    el.innerHTML = list.map(function (p, i) { return productCard(p, { eager: i < 2 && opts && opts.eager }); }).join("");
    hydrate(el);
  }

  /* ------------------------------------------------- option pickers/qty */
  function optionsMarkup(p, prefix) {
    var colors = p.colors.map(function (c, i) {
      return '<label class="swatch" title="' + esc(c.name) + '"><input type="radio" name="color-' + prefix + '" value="' + esc(c.name) + '"' + (i === 0 ? " checked" : "") + ">" +
        '<span style="--sw:' + c.hex + '"></span><span class="sr-only">' + esc(c.name) + "</span></label>";
    }).join("");
    var single = p.sizes.length === 1;
    var sizes = p.sizes.map(function (s) {
      return '<label class="chip"><input type="radio" name="size-' + prefix + '" value="' + esc(s) + '"' + (single ? " checked" : "") + "><span>" + esc(s) + "</span></label>";
    }).join("");
    return '<fieldset class="opt"><legend>Colour: <strong class="js-color-name">' + esc(p.colors[0].name) + "</strong></legend><div class=\"opt__row\">" + colors + "</div></fieldset>" +
      '<fieldset class="opt"><legend>Size' + (single ? ": <strong>" + esc(p.sizes[0]) + "</strong>" : "") + '</legend><div class="opt__row">' + sizes + "</div>" +
      '<p class="opt__error" role="alert" hidden>Please select a size.</p></fieldset>';
  }

  function qtyMarkup(id) {
    return '<div class="qty" data-qty><button type="button" class="qty__btn" data-qty-dec aria-label="Decrease quantity">' + icon("minus", 16) + "</button>" +
      '<input class="qty__input" id="' + id + '" type="text" inputmode="numeric" value="1" aria-label="Quantity" maxlength="2">' +
      '<button type="button" class="qty__btn" data-qty-inc aria-label="Increase quantity">' + icon("plus", 16) + "</button></div>";
  }

  function bindOptions(root) {
    root.addEventListener("change", function (e) {
      if (e.target.matches('input[name^="color-"]')) {
        var n = root.querySelector(".js-color-name"); if (n) n.textContent = e.target.value;
      }
      if (e.target.matches('input[name^="size-"]')) {
        var err = root.querySelector(".opt__error"); if (err) err.hidden = true;
      }
    });
    root.addEventListener("click", function (e) {
      var q = e.target.closest("[data-qty]");
      if (!q) return;
      var input = q.querySelector(".qty__input");
      var v = parseInt(input.value, 10) || 1;
      if (e.target.closest("[data-qty-inc]")) v++;
      if (e.target.closest("[data-qty-dec]")) v--;
      if (e.target.closest("[data-qty-inc],[data-qty-dec]")) input.value = Math.max(1, Math.min(10, v));
    });
    root.addEventListener("blur", function (e) {
      if (e.target.matches(".qty__input")) e.target.value = Math.max(1, Math.min(10, parseInt(e.target.value, 10) || 1));
    }, true);
  }

  /** Reads selected options; returns null (and shows the error) if size is missing. */
  function readSelection(root, prefix) {
    var size = root.querySelector('input[name="size-' + prefix + '"]:checked');
    var color = root.querySelector('input[name="color-' + prefix + '"]:checked');
    var qtyEl = root.querySelector(".qty__input");
    if (!size) {
      var err = root.querySelector(".opt__error"); if (err) err.hidden = false;
      var first = root.querySelector('input[name="size-' + prefix + '"]'); if (first) first.focus();
      return null;
    }
    return { size: size.value, color: color ? color.value : "", qty: Math.max(1, Math.min(10, parseInt(qtyEl && qtyEl.value, 10) || 1)) };
  }

  /* ----------------------------------------------------------- quick view */
  var qvDialog = null, qvId = null;
  function ensureQuickView() {
    if (qvDialog) return qvDialog;
    qvDialog = document.createElement("dialog");
    qvDialog.className = "qv";
    qvDialog.setAttribute("aria-labelledby", "qvTitle");
    document.body.appendChild(qvDialog);
    qvDialog.addEventListener("click", function (e) { if (e.target === qvDialog) qvDialog.close(); });
    qvDialog.addEventListener("close", function () { document.documentElement.classList.remove("no-scroll"); });
    bindOptions(qvDialog);
    return qvDialog;
  }

  function openQuickView(id, hint) {
    var p = S.getProduct(id); if (!p) return;
    var d = ensureQuickView(); qvId = id;
    d.innerHTML =
      '<button type="button" class="qv__close icon-btn" data-qv-close aria-label="Close quick view">' + icon("x", 22) + "</button>" +
      '<div class="qv__grid">' +
        '<div class="qv__media"><span class="img-wrap"><img src="' + p.images[0] + '" alt="' + esc(p.name) + '" width="600" height="800" decoding="async"></span></div>' +
        '<div class="qv__info">' +
          '<p class="eyebrow">' + esc((S.getCategory(p.category) || {}).name || "") + "</p>" +
          '<h2 id="qvTitle" class="qv__title">' + esc(p.name) + "</h2>" +
          priceBlock(p, "price--lg") +
          '<p class="qv__desc">' + esc(p.short) + "</p>" +
          optionsMarkup(p, "qv") +
          '<div class="qv__buy">' + qtyMarkup("qvQty") +
            '<button type="button" class="btn btn--primary" data-qv-add>Add to Cart</button></div>' +
          '<a class="link-arrow" href="product.html?id=' + encodeURIComponent(p.id) + '">View full details ' + icon("right", 16) + "</a>" +
        "</div></div>";
    hydrate(d);
    d.showModal();
    document.documentElement.classList.add("no-scroll");
    if (hint) { var err = d.querySelector(".opt__error"); if (err) { err.textContent = hint; err.hidden = false; } }
  }

  /* ------------------------------------------------------ global actions */
  function onAction(e) {
    var t = e.target.closest("[data-action],[data-qv-close],[data-qv-add]");
    if (!t) return;

    if (t.hasAttribute("data-qv-close")) { qvDialog.close(); return; }
    if (t.hasAttribute("data-qv-add")) {
      var sel = readSelection(qvDialog, "qv"); if (!sel) return;
      S.addToCart(qvId, sel);
      qvDialog.close();
      toast("Added to your cart", { href: "cart.html", linkText: "View cart" });
      return;
    }

    var action = t.getAttribute("data-action"), id = t.getAttribute("data-id");
    if (action === "wishlist") {
      var added = S.toggleWishlist(id);
      toast(added ? "Saved to wishlist" : "Removed from wishlist", added ? { href: "wishlist.html", linkText: "View" } : {});
      return;
    }
    if (action === "quick-view") { openQuickView(id); return; }
    if (action === "add-to-cart") {
      var prod = S.getProduct(id); if (!prod) return;
      if (prod.sizes.length > 1) { openQuickView(id, "Please choose your size to add this item."); return; }
      S.addToCart(id, {});
      toast("Added to your cart", { href: "cart.html", linkText: "View cart" });
    }
  }

  /* --------------------------------------------------------- page chrome */
  function navState() {
    var path = location.pathname.split("/").pop() || "index.html";
    var q = new URLSearchParams(location.search);
    if (path === "index.html" || path === "") return "home";
    if (path === "shop.html") {
      if (q.get("filter") === "new") return "new";
      if (q.get("filter") === "best") return "best";
      return "shop";
    }
    if (path === "about.html") return "about";
    if (path === "contact.html") return "contact";
    if (/^(account|login|register)/.test(path)) return "account";
    return "";
  }

  function renderChrome() {
    var cur = navState();
    var cats = D.categories.filter(function (c) { return c.slug !== "new"; });
    var navLink = function (key, href, label) {
      return '<li><a class="nav-link" href="' + href + '"' + (cur === key ? ' aria-current="page"' : "") + ">" + label + "</a></li>";
    };
    var signedIn = !!window.Auth.current();
    var accountHref = signedIn ? "account.html" : "login.html";
    var catItems = cats.map(function (c) { return '<li><a href="shop.html?category=' + c.slug + '">' + esc(c.name) + "</a></li>"; }).join("");

    var ann = document.getElementById("site-announcement");
    if (ann) {
      ann.className = "announcement";
      ann.innerHTML = '<div class="container"><p class="announcement__text" id="annText" aria-live="polite">' + esc(D.announcements[0]) + "</p></div>";
      var ai = 0;
      if (D.announcements.length > 1 && !window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
        setInterval(function () {
          var el = document.getElementById("annText"); if (!el) return;
          el.classList.add("is-out");
          setTimeout(function () { ai = (ai + 1) % D.announcements.length; el.textContent = D.announcements[ai]; el.classList.remove("is-out"); }, 350);
        }, 4500);
      }
    }

    var header = document.getElementById("site-header");
    if (header) {
      header.outerHTML =
        '<header class="site-header" id="siteHeader"><div class="container header__inner">' +
          '<button type="button" class="icon-btn header__menu" data-open-drawer aria-label="Open menu" aria-controls="mobileDrawer" aria-expanded="false">' + icon("menu", 24) + "</button>" +
          '<a class="logo" href="index.html" aria-label="Ashfi Collection home"><img class="logo__img" src="images/brand/logo-horizontal.svg" alt="Ashfi Collection" width="562" height="176"></a>' +
          '<nav class="primary-nav" aria-label="Primary"><ul>' +
            navLink("home", "index.html", "Home") + navLink("shop", "shop.html", "Shop") + navLink("new", "shop.html?filter=new", "New Arrivals") +
            '<li class="has-dropdown"><a class="nav-link" href="index.html#categories" aria-haspopup="true">Categories ' + icon("down", 14) + '</a><ul class="dropdown">' + catItems + "</ul></li>" +
            navLink("best", "shop.html?filter=best", "Best Sellers") + navLink("about", "about.html", "About Us") + navLink("contact", "contact.html", "Contact") +
          "</ul></nav>" +
          '<div class="header__actions">' +
            '<button type="button" class="icon-btn" data-search-toggle aria-label="Search products" aria-expanded="false" aria-controls="searchPanel">' + icon("search", 22) + "</button>" +
            '<a class="icon-btn header__account' + (signedIn ? " is-on" : "") + '" href="' + accountHref + '" aria-label="' + (signedIn ? "My account" : "Log in") + '">' + icon("user", 22) + "</a>" +
            '<a class="icon-btn header__wish" href="wishlist.html" aria-label="Wishlist">' + icon("heart", 22) + '<span class="count-badge" data-wish-count hidden>0</span></a>' +
            '<a class="icon-btn" href="cart.html" aria-label="Shopping cart">' + icon("bag", 22) + '<span class="count-badge" data-cart-count hidden>0</span></a>' +
          "</div></div>" +
          '<div class="search-panel" id="searchPanel" hidden><div class="container">' +
            '<form class="search-form" action="shop.html" method="get" role="search">' + icon("search", 20) +
              '<input type="search" name="q" id="searchInput" placeholder="Search products…" autocomplete="off" aria-label="Search products">' +
              '<button type="submit" class="btn btn--primary btn--sm">Search</button>' +
              '<button type="button" class="icon-btn" data-search-toggle aria-label="Close search">' + icon("x", 20) + "</button></form>" +
            '<ul class="search-results" id="searchResults" aria-live="polite"></ul>' +
          "</div></div></header>";
    }

    var catDrawer = cats.map(function (c) { return '<li><a href="shop.html?category=' + c.slug + '">' + esc(c.name) + "</a></li>"; }).join("");
    var drawer = document.createElement("div");
    drawer.innerHTML =
      '<div class="overlay" data-close-drawer hidden></div>' +
      '<aside class="drawer" id="mobileDrawer" aria-label="Menu" aria-hidden="true" inert>' +
        '<div class="drawer__head"><a class="logo" href="index.html"><img class="logo__img" src="images/brand/logo-horizontal.svg" alt="Ashfi Collection" width="562" height="176"></a>' +
        '<button type="button" class="icon-btn" data-close-drawer aria-label="Close menu">' + icon("x", 22) + "</button></div>" +
        '<nav aria-label="Mobile"><ul class="drawer__list">' +
          navLink("home", "index.html", "Home") + navLink("shop", "shop.html", "Shop") + navLink("new", "shop.html?filter=new", "New Arrivals") +
          navLink("best", "shop.html?filter=best", "Best Sellers") +
        "</ul>" +
        '<p class="drawer__label" id="drawerCats">Categories</p><ul class="drawer__list drawer__list--sub" aria-labelledby="drawerCats">' + catDrawer + "</ul>" +
        '<ul class="drawer__list">' + navLink("account", accountHref, signedIn ? "My Account" : "Log In / Register") + navLink("", "wishlist.html", "Wishlist") + navLink("", "track-order.html", "Track Order") + navLink("", "offers.html", "Offers") +
          navLink("about", "about.html", "About Us") + navLink("contact", "contact.html", "Contact") + "</ul></nav>" +
        '<div class="drawer__foot"><a href="tel:' + D.brand.phone.replace(/[^+\d]/g, "") + '">' + icon("phone", 18) + " " + esc(D.brand.phone) + "</a>" +
        '<div class="social"><a href="' + D.brand.facebook + '" target="_blank" rel="noopener" aria-label="Facebook">' + icon("facebook", 20) + "</a>" +
        '<a href="' + D.brand.instagram + '" target="_blank" rel="noopener" aria-label="Instagram">' + icon("instagram", 20) + "</a></div></div>" +
      "</aside>";
    while (drawer.firstChild) document.body.appendChild(drawer.firstChild);

    var footer = document.getElementById("site-footer");
    if (footer) {
      footer.outerHTML =
        '<footer class="site-footer"><div class="container"><div class="footer__grid">' +
          '<div class="footer__brand"><a class="logo logo--light" href="index.html"><img class="logo__img" src="images/brand/logo-horizontal-light.svg" alt="Ashfi Collection" width="562" height="176"></a>' +
            '<p class="footer__tag">' + esc(D.brand.tagline) + '</p><p class="footer__bn" lang="bn">' + esc(D.brand.taglineBn) + "</p>" +
            "<p>Elegant, comfortable and thoughtfully finished clothing for the modern Bangladeshi woman.</p>" +
            '<div class="social"><a href="' + D.brand.facebook + '" target="_blank" rel="noopener" aria-label="Ashfi Collection on Facebook">' + icon("facebook", 20) + "</a>" +
            '<a href="' + D.brand.instagram + '" target="_blank" rel="noopener" aria-label="Ashfi Collection on Instagram">' + icon("instagram", 20) + "</a></div></div>" +
          '<nav aria-label="Quick links"><h2 class="footer__h">Quick Links</h2><ul>' +
            '<li><a href="index.html">Home</a></li><li><a href="shop.html">Shop</a></li><li><a href="shop.html?filter=new">New Arrivals</a></li>' +
            '<li><a href="shop.html?filter=best">Best Sellers</a></li><li><a href="offers.html">Offers</a></li><li><a href="size-guide.html">Size Guide</a></li><li><a href="about.html">About Us</a></li></ul></nav>' +
          '<nav aria-label="Customer support"><h2 class="footer__h">Customer Support</h2><ul>' +
            '<li><a href="' + accountHref + '">My Account</a></li><li><a href="track-order.html">Track Order</a></li><li><a href="contact.html">Contact Us</a></li><li><a href="faq.html">FAQs</a></li><li><a href="policy.html#delivery">Delivery Policy</a></li>' +
            '<li><a href="policy.html#returns">Return &amp; Exchange</a></li><li><a href="policy.html#privacy">Privacy Policy</a></li><li><a href="terms.html">Terms &amp; Conditions</a></li><li><a href="policy.html#credits">Photo Credits</a></li></ul></nav>' +
          '<div><h2 class="footer__h">Get in Touch</h2><ul class="footer__contact">' +
            "<li>" + icon("pin", 18) + "<span>" + esc(D.brand.address) + "</span></li>" +
            '<li>' + icon("phone", 18) + '<a href="tel:' + D.brand.phone.replace(/[^+\d]/g, "") + '">' + esc(D.brand.phone) + "</a></li>" +
            '<li>' + icon("mail", 18) + '<a href="mailto:' + D.brand.email + '">' + esc(D.brand.email) + "</a></li>" +
            "<li>" + icon("clock", 18) + "<span>" + esc(D.brand.hours) + "</span></li></ul></div>" +
        "</div>" +
        '<div class="footer__bottom"><p>&copy; <span id="year">2026</span> Ashfi Collection. All rights reserved.</p>' +
        "<p>Cash on Delivery &middot; Nationwide Delivery</p></div></div></footer>";
      var y = document.getElementById("year"); if (y) y.textContent = new Date().getFullYear();
    }

    var bn = document.createElement("nav");
    bn.className = "bottom-nav"; bn.setAttribute("aria-label", "Quick navigation");
    var item = function (href, ic, label, key, extra) {
      return '<a href="' + href + '"' + (cur === key ? ' aria-current="page"' : "") + (extra || "") + ">" + icon(ic, 22) + "<span>" + label + "</span></a>";
    };
    bn.innerHTML = item("index.html", "home", "Home", "home") + item("shop.html", "sliders", "Shop", "shop") +
      '<button type="button" data-open-drawer aria-controls="mobileDrawer">' + icon("grid", 22) + "<span>Categories</span></button>" +
      item(accountHref, "user", "Account", "account") +
      '<a href="cart.html"' + (location.pathname.indexOf("cart") > -1 ? ' aria-current="page"' : "") + ">" + icon("bag", 22) + '<span>Cart</span><i class="count-badge" data-cart-count hidden>0</i></a>';
    document.body.appendChild(bn);

    var top = document.createElement("button");
    top.type = "button"; top.className = "to-top"; top.setAttribute("aria-label", "Back to top"); top.hidden = true;
    top.innerHTML = icon("up", 20);
    top.addEventListener("click", function () { window.scrollTo({ top: 0, behavior: "smooth" }); });
    document.body.appendChild(top);

    var tb = document.createElement("div");
    tb.id = "toasts"; tb.className = "toasts"; tb.setAttribute("aria-live", "polite");
    document.body.appendChild(tb);

    window.addEventListener("scroll", function () {
      var h = document.getElementById("siteHeader");
      if (h) h.classList.toggle("is-scrolled", window.scrollY > 8);
      top.hidden = window.scrollY < 700;
    }, { passive: true });
  }

  function syncCounts() {
    var c = S.cartCount(), w = S.getWishlist().length;
    Array.prototype.forEach.call(document.querySelectorAll("[data-cart-count]"), function (el) { el.textContent = c; el.hidden = c === 0; });
    Array.prototype.forEach.call(document.querySelectorAll("[data-wish-count]"), function (el) { el.textContent = w; el.hidden = w === 0; });
  }

  /* ---------------------------------------------------- drawer & search */
  function setDrawer(open) {
    var dr = document.getElementById("mobileDrawer"), ov = document.querySelector(".overlay");
    if (!dr) return;
    dr.classList.toggle("is-open", open);
    dr.setAttribute("aria-hidden", open ? "false" : "true");
    if (open) dr.removeAttribute("inert"); else dr.setAttribute("inert", "");
    ov.hidden = !open;
    document.documentElement.classList.toggle("no-scroll", open);
    Array.prototype.forEach.call(document.querySelectorAll(".header__menu"), function (b) { b.setAttribute("aria-expanded", open ? "true" : "false"); });
    if (open) { var f = dr.querySelector("a,button"); if (f) f.focus(); }
  }

  function setSearch(open) {
    var panel = document.getElementById("searchPanel"); if (!panel) return;
    panel.hidden = !open;
    Array.prototype.forEach.call(document.querySelectorAll('[aria-controls="searchPanel"]'), function (b) { b.setAttribute("aria-expanded", open ? "true" : "false"); });
    if (open) document.getElementById("searchInput").focus();
  }

  function searchProducts(term) {
    term = term.trim().toLowerCase(); if (!term) return [];
    return D.products.filter(function (p) {
      var cat = (S.getCategory(p.category) || {}).name || "";
      return (p.name + " " + cat + " " + p.short + " " + p.colors.map(function (c) { return c.name; }).join(" ")).toLowerCase().indexOf(term) !== -1;
    });
  }
  window.searchProducts = searchProducts;

  function bindChrome() {
    document.addEventListener("click", function (e) {
      if (e.target.closest("[data-open-drawer]")) { setDrawer(true); return; }
      if (e.target.closest("[data-close-drawer]")) { setDrawer(false); return; }
      if (e.target.closest("[data-search-toggle]")) { setSearch(document.getElementById("searchPanel").hidden); return; }
      if (e.target.closest("#mobileDrawer a")) setDrawer(false);
      var pw = e.target.closest("[data-pw-toggle]");
      if (pw) {
        var field = document.getElementById(pw.getAttribute("data-pw-toggle")), show = field.type === "password";
        field.type = show ? "text" : "password";
        pw.textContent = show ? "Hide" : "Show";
        pw.setAttribute("aria-pressed", show ? "true" : "false");
        return;
      }
      onAction(e);
    });
    document.addEventListener("keydown", function (e) {
      if (e.key !== "Escape") return;
      setDrawer(false); var sp = document.getElementById("searchPanel"); if (sp && !sp.hidden) setSearch(false);
    });

    var input = document.getElementById("searchInput"), list = document.getElementById("searchResults"), timer;
    if (input) input.addEventListener("input", function () {
      clearTimeout(timer);
      timer = setTimeout(function () {
        var res = searchProducts(input.value).slice(0, 5);
        if (!input.value.trim()) { list.innerHTML = ""; return; }
        if (!res.length) { list.innerHTML = '<li class="search-results__empty">No products match “' + esc(input.value.trim()) + "”. Try “kurti” or “saree”.</li>"; return; }
        list.innerHTML = res.map(function (p) {
          return '<li><a href="product.html?id=' + encodeURIComponent(p.id) + '"><img src="' + p.images[0] + '" alt="" width="48" height="64" loading="lazy">' +
            '<span class="search-results__name">' + esc(p.name) + "</span><span class=\"search-results__price\">" + S.money(S.unitPrice(p)) + "</span></a></li>";
        }).join("") + '<li class="search-results__all"><a href="shop.html?q=' + encodeURIComponent(input.value.trim()) + '">See all results ' + icon("right", 16) + "</a></li>";
      }, 120);
    });
  }

  /* ---------------------------------------------------------------- init */
  function init() {
    renderChrome();
    bindChrome();
    syncCounts();
    hydrate();
    document.addEventListener("ashfi:cart", syncCounts);
    document.addEventListener("ashfi:wishlist", function () { syncCounts(); syncWishlistButtons(); });
    window.addEventListener("storage", function () { syncCounts(); syncWishlistButtons(); document.dispatchEvent(new CustomEvent("ashfi:cart")); });
    document.dispatchEvent(new CustomEvent("ashfi:ready"));
  }

  window.UI = {
    icon: icon, stars: stars, toast: toast, hydrate: hydrate, productCard: productCard, renderProducts: renderProducts,
    priceBlock: priceBlock, optionsMarkup: optionsMarkup, qtyMarkup: qtyMarkup, bindOptions: bindOptions, readSelection: readSelection,
    openQuickView: openQuickView, fieldError: fieldError, couponMarkup: couponMarkup, bindCoupon: bindCoupon,
    setCanonical: setCanonical, jsonLd: jsonLd
  };

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init); else init();
})();
