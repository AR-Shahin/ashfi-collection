/**
 * Ashfi Collection – front-end behaviour for the Laravel (Blade) site.
 * Every page is rendered on the server and every form works without JavaScript;
 * this file adds the niceties: drawer, live search, quick view, toasts, and
 * submitting cart / wishlist / coupon forms with fetch so the page doesn't reload.
 */
(function () {
  "use strict";

  var A = window.ASHFI || {};
  var token = (document.querySelector('meta[name="csrf-token"]') || {}).content || "";
  var $ = function (sel, root) { return (root || document).querySelector(sel); };
  var $$ = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };
  var reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function esc(s) {
    return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  }

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

  /* ---------------------------------------------------------------- toast */
  function toast(message, opts) {
    opts = opts || {};
    var box = document.getElementById("toasts");
    if (!box || !message) return;
    var el = document.createElement("div");
    el.className = "toast" + (opts.type === "error" ? " toast--error" : "");
    el.innerHTML = '<span class="toast__icon">' + icon(opts.type === "error" ? "info" : "check", 18) + "</span>" +
      "<span>" + esc(message) + "</span>" +
      (opts.href ? '<a class="toast__link" href="' + esc(opts.href) + '">' + esc(opts.linkText || "View") + "</a>" : "");
    box.appendChild(el);
    requestAnimationFrame(function () { el.classList.add("is-in"); });
    setTimeout(function () {
      el.classList.remove("is-in");
      setTimeout(function () { el.remove(); }, 300);
    }, opts.duration || 3200);
  }

  /* -------------------------------------------------------------- network */
  function send(form, extra) {
    var data = new FormData(form);
    Object.keys(extra || {}).forEach(function (k) { data.set(k, extra[k]); });
    return fetch(form.action, {
      method: "POST",
      headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": token },
      body: data,
      credentials: "same-origin"
    }).then(function (r) {
      return r.json().catch(function () { return {}; }).then(function (j) { j.status = r.status; return j; });
    });
  }

  function firstError(res) {
    if (res.errors) { var k = Object.keys(res.errors)[0]; if (k) return res.errors[k][0]; }
    return res.message || "Something went wrong. Please try again.";
  }

  function syncCounts(res) {
    if (typeof res.cartCount === "number") $$("[data-cart-count]").forEach(function (el) { el.textContent = res.cartCount; el.hidden = !res.cartCount; });
    if (typeof res.wishCount === "number") $$("[data-wish-count]").forEach(function (el) { el.textContent = res.wishCount; el.hidden = !res.wishCount; });
  }

  /* --------------------------------------------------- image skeletons */
  function hydrate(root) {
    $$(".img-wrap", root).forEach(function (w) {
      var img = w.querySelector("img");
      if (!img || w.classList.contains("is-loaded")) return;
      var done = function () { w.classList.add("is-loaded"); };
      if (img.complete && img.naturalWidth) done();
      else { img.addEventListener("load", done, { once: true }); img.addEventListener("error", done, { once: true }); }
    });
  }

  /* --------------------------------------------- options, sizes, qty */
  function clampQty(v) { return Math.max(1, Math.min(10, parseInt(v, 10) || 1)); }

  document.addEventListener("change", function (e) {
    var t = e.target;
    if (t.matches('[data-options] input[name="color"]')) {
      var n = $(".js-color-name", t.closest("form")); if (n) n.textContent = t.value;
    }
    if (t.matches('[data-options] input[name="size"]')) {
      var err = $(".opt__error", t.closest("form")); if (err) err.hidden = true;
    }
  });
  document.addEventListener("blur", function (e) {
    if (e.target.matches && e.target.matches("[data-qty] .qty__input")) e.target.value = clampQty(e.target.value);
  }, true);

  /** Size must be picked before the form is sent. Returns false (and shows why) if it isn't. */
  function checkSize(form) {
    var sizes = $$('input[name="size"]', form);
    if (!sizes.length || sizes.some(function (r) { return r.checked; })) return true;
    var err = $(".opt__error", form); if (err) err.hidden = false;
    sizes[0].focus();
    return false;
  }

  /* ----------------------------------------------------------- quick view */
  var qv = null;
  function openQuickView(url) {
    if (!qv) {
      qv = document.createElement("dialog");
      qv.className = "qv";
      qv.setAttribute("aria-labelledby", "qvTitle");
      document.body.appendChild(qv);
      qv.addEventListener("click", function (e) { if (e.target === qv || e.target.closest("[data-qv-close]")) qv.close(); });
      qv.addEventListener("close", function () { document.documentElement.classList.remove("no-scroll"); });
    }
    return fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" }, credentials: "same-origin" })
      .then(function (r) { if (!r.ok) throw new Error(r.status); return r.text(); })
      .then(function (html) {
        qv.innerHTML = html;
        hydrate(qv);
        if (!qv.open) qv.showModal();
        document.documentElement.classList.add("no-scroll");
      });
  }

  /* ------------------------------------------------------------ AJAX forms */
  function setWishButtons(slug, saved) {
    $$('[data-action="wishlist"][data-id="' + slug + '"]').forEach(function (b) {
      b.setAttribute("aria-pressed", saved ? "true" : "false");
      b.classList.toggle("is-active", saved);
      var label = b.querySelector(".js-wish-label");
      if (label) label.textContent = saved ? "Saved" : "Wishlist";
      else b.setAttribute("aria-label", saved ? "Remove from wishlist" : "Add to wishlist");
    });
  }

  var handlers = {
    cart: function (form, submitter) {
      if (form.hasAttribute("data-options") && !checkSize(form)) return;
      var extra = {};
      if (submitter && submitter.name) extra[submitter.name] = submitter.value;
      return send(form, extra).then(function (res) {
        if (res.redirect) { location.href = res.redirect; return; }
        if (res.needsOptions) { var err = $(".opt__error", form); if (err) { err.textContent = res.message; err.hidden = false; } return; }
        syncCounts(res);
        if (form.hasAttribute("data-close-dialog") && qv && qv.open) qv.close();
        toast(res.ok ? res.message : firstError(res), res.ok ? { href: res.href, linkText: res.linkText } : { type: "error" });
      });
    },
    wishlist: function (form) {
      return send(form).then(function (res) {
        if (!res.ok) { toast(firstError(res), { type: "error" }); return; }
        syncCounts(res);
        var btn = $('[data-action="wishlist"]', form);
        setWishButtons(btn.getAttribute("data-id"), res.saved);
        toast(res.message, { href: res.href, linkText: res.linkText });
        // on the wishlist page, take the card away
        if (!res.saved && form.closest("[data-wishlist-page]")) {
          var card = form.closest(".product-card"); if (card) card.remove();
          if (!$("[data-wishlist-page] .product-card")) location.reload();
        }
      });
    },
    /* cart lines, coupons – the server sends back the re-rendered summary */
    summary: function (form, submitter) {
      var root = form.closest("[data-summary-root]");
      var extra = { view: root && root.getAttribute("data-view") === "checkout" ? "checkout" : "cart" };
      var area = $('input[name="area"]:checked'); if (area) extra.area = area.value;
      if (submitter && submitter.name) extra[submitter.name] = submitter.value;
      return send(form, extra).then(function (res) {
        syncCounts(res);
        if (res.html != null && root) {
          if (res.empty && extra.view === "checkout") { location.reload(); return; }
          root.innerHTML = res.html;
          hydrate(root);
        }
        if (!res.ok) {
          var msg = root && $(".coupon__msg", root);
          if (msg && res.status === 422) { msg.textContent = firstError(res); msg.classList.add("is-error"); var input = $("#couponCode", root); if (input) input.focus(); }
          else toast(firstError(res), { type: "error" });
          return;
        }
        toast(res.message);
      });
    },
    /* newsletter / contact: message shown next to the form */
    message: function (form) {
      var msg = $(form.getAttribute("data-msg"));
      $$("[aria-invalid]", form).forEach(function (f) { f.removeAttribute("aria-invalid"); });
      $$(".field__error", form).forEach(function (p) { p.textContent = ""; });
      return send(form).then(function (res) {
        if (res.ok) {
          if (msg) { msg.className = "news-msg is-ok"; msg.textContent = res.message; }
          form.reset();
          return;
        }
        var errors = res.errors || {};
        Object.keys(errors).forEach(function (name) {
          var field = form.elements[name]; if (!field || !field.id) return;
          field.setAttribute("aria-invalid", "true");
          var p = document.getElementById(field.id + "Err"); if (p) p.textContent = errors[name][0];
        });
        if (msg && (!Object.keys(errors).length || !document.getElementById((form.elements[Object.keys(errors)[0]] || {}).id + "Err"))) {
          msg.className = "news-msg is-error"; msg.textContent = firstError(res);
        }
        var bad = $('[aria-invalid="true"]', form); if (bad) bad.focus();
      });
    },
    toast: function (form) {
      return send(form).then(function (res) {
        syncCounts(res);
        toast(res.ok ? res.message : firstError(res), res.ok ? { href: res.href, linkText: res.linkText } : { type: "error" });
      });
    }
  };

  document.addEventListener("submit", function (e) {
    var form = e.target;
    var question = form.getAttribute("data-confirm");
    if (question && !window.confirm(question)) { e.preventDefault(); return; }
    var kind = form.getAttribute("data-ajax");
    if (!kind || !handlers[kind] || !window.fetch) {
      if (form.hasAttribute("data-options") && !checkSize(form)) e.preventDefault();
      return;
    }
    e.preventDefault();
    if (form.dataset.busy) return;
    form.dataset.busy = "1";
    var p = handlers[kind](form, e.submitter);
    var release = function () { delete form.dataset.busy; };
    if (p && p.then) p.then(release, function () { release(); form.submit(); }); else release();
  });

  /* --------------------------------------------------------------- clicks */
  document.addEventListener("click", function (e) {
    var t = e.target;
    if (t.closest("[data-open-drawer]")) { setDrawer(true); return; }
    if (t.closest("[data-close-drawer]")) { setDrawer(false); return; }
    if (t.closest("[data-search-toggle]")) { setSearch($("#searchPanel").hidden); return; }
    if (t.closest("#mobileDrawer a")) setDrawer(false);

    var pw = t.closest("[data-pw-toggle]");
    if (pw) {
      var field = document.getElementById(pw.getAttribute("data-pw-toggle")), show = field.type === "password";
      field.type = show ? "text" : "password";
      pw.textContent = show ? "Hide" : "Show";
      pw.setAttribute("aria-pressed", show ? "true" : "false");
      return;
    }

    // quick view (and "Add to Cart" on cards for products that need a size)
    var q = t.closest('[data-action="quick-view"][data-url], [data-action="add-to-cart"][data-url]');
    if (q && window.fetch && window.HTMLDialogElement) {
      e.preventDefault();
      openQuickView(q.getAttribute("data-url")).catch(function () { location.href = q.href; });
      return;
    }

    // product page / quick view quantity buttons
    var qb = t.closest("[data-qty-inc],[data-qty-dec]");
    if (qb && qb.closest("[data-qty]")) {
      var input = $(".qty__input", qb.closest("[data-qty]"));
      input.value = clampQty((parseInt(input.value, 10) || 1) + (qb.hasAttribute("data-qty-inc") ? 1 : -1));
      return;
    }

    // coupon code copy (offers page)
    var copy = t.closest("[data-copy]");
    if (copy) {
      var code = copy.getAttribute("data-copy");
      var done = function () { toast(code + " copied", { href: "/shop", linkText: "Shop now" }); };
      if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(code).then(done, function () { toast("Your code is " + code); });
      else toast("Your code is " + code);
      return;
    }

    // product gallery
    var gallery = t.closest("[data-gallery]");
    if (gallery) {
      var thumbs = $$(".thumb", gallery), cur = thumbs.findIndex(function (b) { return b.hasAttribute("aria-current"); });
      var thumb = t.closest(".thumb"), nav = t.closest("[data-nav]"), next = -1;
      if (thumb) next = thumbs.indexOf(thumb);
      if (nav) next = (cur + parseInt(nav.getAttribute("data-nav"), 10) + thumbs.length) % thumbs.length;
      if (next > -1 && thumbs[next]) {
        var wrap = $("#mainWrap"), img = $("#mainImg");
        wrap.classList.remove("is-loaded");
        img.src = thumbs[next].getAttribute("data-src");
        hydrate(wrap.parentNode);
        thumbs.forEach(function (b, i) { if (i === next) b.setAttribute("aria-current", "true"); else b.removeAttribute("aria-current"); });
      }
    }

    // horizontal product scroller (home)
    var sb = t.closest("[data-scroll]");
    if (sb) {
      var sc = document.getElementById(sb.getAttribute("aria-controls")), card = sc && sc.firstElementChild;
      var w = card ? card.getBoundingClientRect().width + 18 : 280;
      if (sc) sc.scrollBy({ left: parseInt(sb.getAttribute("data-scroll"), 10) * w * 2, behavior: reduced ? "auto" : "smooth" });
    }
  });

  // cart page: typing a quantity submits that line
  document.addEventListener("change", function (e) {
    if (e.target.matches("[data-qty-form] .qty__input")) {
      var f = e.target.closest("form");
      e.target.value = Math.max(0, Math.min(10, parseInt(e.target.value, 10) || 0));
      if (f.requestSubmit) f.requestSubmit(); else f.submit();
    }
  });

  /* --------------------------------------------------- drawer & search */
  function setDrawer(open) {
    var dr = $("#mobileDrawer"), ov = $(".overlay[data-close-drawer]");
    if (!dr) return;
    dr.classList.toggle("is-open", open);
    dr.setAttribute("aria-hidden", open ? "false" : "true");
    if (open) dr.removeAttribute("inert"); else dr.setAttribute("inert", "");
    if (ov) ov.hidden = !open;
    document.documentElement.classList.toggle("no-scroll", open);
    $$(".header__menu").forEach(function (b) { b.setAttribute("aria-expanded", open ? "true" : "false"); });
    if (open) { var f = dr.querySelector("a,button"); if (f) f.focus(); }
  }

  function setSearch(open) {
    var panel = $("#searchPanel"); if (!panel) return;
    panel.hidden = !open;
    $$('[aria-controls="searchPanel"]').forEach(function (b) { b.setAttribute("aria-expanded", open ? "true" : "false"); });
    if (open) $("#searchInput").focus();
  }

  document.addEventListener("keydown", function (e) {
    if (e.key !== "Escape") return;
    setDrawer(false);
    var sp = $("#searchPanel"); if (sp && !sp.hidden) setSearch(false);
    var filters = $("#filters"); if (filters && filters.classList.contains("is-open")) setFilters(false);
  });

  function bindSearch() {
    var input = $("#searchInput"), list = $("#searchResults"), timer, seq = 0;
    if (!input || !A.routes) return;
    input.addEventListener("input", function () {
      clearTimeout(timer);
      var term = input.value.trim();
      if (!term) { list.innerHTML = ""; return; }
      timer = setTimeout(function () {
        var mine = ++seq;
        fetch(A.routes.search + "?q=" + encodeURIComponent(term), { headers: { "Accept": "application/json" } })
          .then(function (r) { return r.json(); })
          .then(function (res) {
            if (mine !== seq) return;
            if (!res.results.length) { list.innerHTML = '<li class="search-results__empty">No products match “' + esc(term) + "”. Try “kurti” or “saree”.</li>"; return; }
            list.innerHTML = res.results.map(function (p) {
              return '<li><a href="' + esc(p.url) + '"><img src="' + esc(p.image) + '" alt="" width="48" height="64" loading="lazy">' +
                '<span class="search-results__name">' + esc(p.name) + '</span><span class="search-results__price">' + esc(p.price) + "</span></a></li>";
            }).join("") + '<li class="search-results__all"><a href="' + esc(res.all) + '">See all results ' + icon("right", 16) + "</a></li>";
          })
          .catch(function () {});
      }, 150);
    });
  }

  /* ------------------------------------------------------------- shop page */
  function setFilters(open) {
    var f = $("#filters"); if (!f) return;
    f.classList.toggle("is-open", open);
    $("#filterOverlay").hidden = !open;
    document.documentElement.classList.toggle("no-scroll", open);
    $("#filterOpen").setAttribute("aria-expanded", open ? "true" : "false");
    if (open) $("#filterClose").focus(); else $("#filterOpen").focus();
  }

  function bindShop() {
    var form = $("#shopForm"); if (!form) return;
    var drawerMode = function () { return $("#filters").classList.contains("is-open"); };
    var submit = function () {
      // keep the URL tidy: drop empty fields
      $$("input, select", document).forEach(function (el) {
        if (el.form === form && el.name && (el.value === "" || (el.name === "sort" && el.value === "latest"))) el.disabled = true;
      });
      form.submit();
    };
    document.addEventListener("change", function (e) {
      var el = e.target;
      if (el.form !== form || el.name === "q" || el.name === "min" || el.name === "max") return;
      if (drawerMode() && el.name !== "sort") return; // phones: wait for "Show results"
      submit();
    });
    $$("#pMin, #pMax").forEach(function (el) {
      el.addEventListener("change", function () { if (!drawerMode()) submit(); });
    });
    form.addEventListener("submit", function (e) { e.preventDefault(); submit(); });
    $("#shopQ").addEventListener("keydown", function (e) { if (e.key === "Enter") { e.preventDefault(); submit(); } });
    $("#filterOpen").addEventListener("click", function () { setFilters(true); });
    $("#filterClose").addEventListener("click", function () { setFilters(false); });
    $("#filterOverlay").addEventListener("click", function () { setFilters(false); });
  }

  /* --------------------------------------------------------- checkout page */
  function bindCheckout() {
    var form = $("[data-checkout]"); if (!form) return;
    var summary = $("#checkoutSummary");
    var refresh = function () {
      var area = $('input[name="area"]:checked', form);
      var url = summary.getAttribute("data-refresh") + "?area=" + encodeURIComponent(area ? area.value : "inside");
      fetch(url, { headers: { "Accept": "application/json" }, credentials: "same-origin" })
        .then(function (r) { return r.json(); })
        .then(function (res) { if (res.html != null) { summary.innerHTML = res.html; hydrate(summary); } })
        .catch(function () {});
    };
    var setArea = function (district) {
      var r = $('input[name="area"][value="' + (district === "Dhaka" ? "inside" : "outside") + '"]', form);
      if (r && !r.checked) { r.checked = true; refresh(); }
    };
    form.addEventListener("change", function (e) {
      if (e.target.name === "area") refresh();
      if (e.target.name === "district" && e.target.value) setArea(e.target.value);
      if (e.target.id === "savedAddress") {
        var list = JSON.parse(e.target.getAttribute("data-addresses") || "[]");
        var a = list.filter(function (x) { return String(x.id) === e.target.value; })[0];
        var row = $("#saveAddressRow"); if (row) row.hidden = !!a;
        ["name", "phone", "address"].forEach(function (k) { form.elements[k].value = a ? a[k] : (k === "address" ? "" : form.elements[k].value); });
        form.elements.district.value = a ? a.district : "";
        if (a) setArea(a.district);
      }
    });
    form.addEventListener("submit", function () {
      var b = form.querySelector('button[type="submit"]');
      if (b) { b.disabled = true; b.textContent = "Placing order…"; }
    });
  }

  /* ---------------------------------------------- clear errors while typing */
  document.addEventListener("input", function (e) {
    var f = e.target;
    if (f.getAttribute && f.getAttribute("aria-invalid") === "true") {
      f.removeAttribute("aria-invalid");
      var p = document.getElementById(f.id + "Err"); if (p) p.textContent = "";
    }
  });

  /* ----------------------------------------------------------------- init */
  function init() {
    hydrate();
    bindSearch();
    bindShop();
    bindCheckout();

    // announcement bar
    var ann = A.announcements || [], ai = 0;
    if (ann.length > 1 && !reduced) {
      setInterval(function () {
        var el = $("#annText"); if (!el) return;
        el.classList.add("is-out");
        setTimeout(function () { ai = (ai + 1) % ann.length; el.textContent = ann[ai]; el.classList.remove("is-out"); }, 350);
      }, 4500);
    }

    // header shadow + back-to-top
    var top = $(".to-top");
    if (top) top.addEventListener("click", function () { window.scrollTo({ top: 0, behavior: reduced ? "auto" : "smooth" }); });
    window.addEventListener("scroll", function () {
      var h = $("#siteHeader"); if (h) h.classList.toggle("is-scrolled", window.scrollY > 8);
      if (top) top.hidden = window.scrollY < 700;
    }, { passive: true });

    // account menu scrolls sideways on phones – bring the current page into view
    var on = $('.account__nav [aria-current="page"]');
    if (on) { var list = on.closest("ul"); list.scrollLeft += on.getBoundingClientRect().left - list.getBoundingClientRect().left; }

    // first invalid field gets focus after a server-side validation error
    var bad = $('main [aria-invalid="true"]'); if (bad) bad.focus();

    // flash message from the server (after a normal form post)
    var flash = $("#flashToast");
    if (flash) { try { var f = JSON.parse(flash.textContent); toast(f.message, f); } catch (err) { /* ignore */ } }
  }

  window.UI = { icon: icon, toast: toast, hydrate: hydrate };
  window.Store = { escape: esc };

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init); else init();
})();
