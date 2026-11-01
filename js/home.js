/* Homepage: categories, product sections, testimonials, newsletter. */
(function () {
  "use strict";
  var S = window.Store, D = S.data, U = window.UI, esc = S.escape;

  function init() {
    // Categories
    document.getElementById("catGrid").innerHTML = D.categories.map(function (c) {
      var href = c.href || "shop.html?category=" + c.slug;
      return '<a class="cat-card" href="' + href + '">' +
        '<span class="img-wrap"><img src="' + c.image + '" alt="" width="600" height="800" loading="lazy" decoding="async"></span>' +
        '<span class="cat-card__label"><span class="cat-card__name">' + esc(c.name) + '</span>' +
        '<span class="cat-card__go">Explore ' + U.icon("right", 14) + "</span></span></a>";
    }).join("");
    U.hydrate(document.getElementById("catGrid"));

    // Featured
    var featured = D.featuredIds.map(S.getProduct).filter(Boolean);
    U.renderProducts(document.getElementById("featuredGrid"), featured);

    // New arrivals – newest first
    var fresh = D.products.filter(function (p) { return p.isNew; })
      .sort(function (a, b) { return b.createdAt.localeCompare(a.createdAt); });
    var scroller = document.getElementById("newScroller");
    U.renderProducts(scroller, fresh);
    var step = function (dir) {
      var card = scroller.firstElementChild;
      var w = card ? card.getBoundingClientRect().width + 18 : 280;
      scroller.scrollBy({ left: dir * w * 2, behavior: "smooth" });
    };
    document.getElementById("newPrev").addEventListener("click", function () { step(-1); });
    document.getElementById("newNext").addEventListener("click", function () { step(1); });

    // Best sellers
    U.renderProducts(document.getElementById("bestGrid"), D.products.filter(function (p) { return p.isBestSeller; }));

    // Testimonials (demo content)
    document.getElementById("reviewGrid").innerHTML = D.testimonials.map(function (t) {
      return '<figure class="review" style="margin:0">' + U.stars(t.rating) +
        "<blockquote style=\"margin:0\"><p>" + esc(t.text) + "</p></blockquote>" +
        '<figcaption class="review__who"><span class="avatar" aria-hidden="true">' + esc(t.name.charAt(0)) + "</span>" +
        '<span><span class="review__name">' + esc(t.name) + '</span><span class="review__place">' + esc(t.place) + "</span></span></figcaption></figure>";
    }).join("");

    // Newsletter – frontend-only validation + demo success state
    var form = document.getElementById("newsForm"), input = document.getElementById("newsEmail"), msg = document.getElementById("newsMsg");
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var ok = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(input.value.trim());
      input.setAttribute("aria-invalid", ok ? "false" : "true");
      msg.className = "news-msg " + (ok ? "is-ok" : "is-error");
      if (!ok) { msg.textContent = "Please enter a valid email address."; input.focus(); return; }
      msg.textContent = "Thank you for subscribing! (Demo – no email was actually saved.)";
      form.reset();
      // TODO (backend): POST the email to your newsletter endpoint here.
    });
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init); else init();
})();
