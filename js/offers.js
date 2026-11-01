/* Offers page: coupon cards and sale products, both from data.js. */
(function () {
  "use strict";
  var S = window.Store, D = S.data, U = window.UI, esc = S.escape;

  function card(c) {
    return '<article class="offer"><div class="offer__icon">' + U.icon(c.type === "shipping" ? "truck" : "tag", 24) + "</div>" +
      "<h3>" + esc(c.title) + "</h3><p>" + esc(c.detail) + "</p>" +
      (c.expires ? "<small>Valid until " + esc(new Date(c.expires).toLocaleDateString("en-GB", { day: "numeric", month: "long", year: "numeric" })) + "</small>" : "") +
      '<button type="button" class="offer__code" data-copy="' + esc(c.code) + '" aria-label="Copy coupon code ' + esc(c.code) + '"><span>' + esc(c.code) + "</span>" + U.icon("copy", 16) + "</button></article>";
  }

  document.addEventListener("ashfi:ready", function () {
    var grid = document.getElementById("offerGrid");
    grid.innerHTML = D.coupons.filter(function (c) { return !S.isCouponExpired(c); }).map(card).join("") ||
      '<p class="empty__text">There are no coupon codes right now. Check back soon.</p>';
    U.renderProducts(document.getElementById("saleGrid"), D.products.filter(function (p) { return S.discountPct(p) > 0; }));
    grid.addEventListener("click", function (e) {
      var b = e.target.closest("[data-copy]"); if (!b) return;
      var code = b.getAttribute("data-copy"), done = function () { U.toast(code + " copied", { href: "shop.html", linkText: "Shop now" }); };
      if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(code).then(done, function () { U.toast("Your code is " + code); });
      else U.toast("Your code is " + code);
    });
  });
})();
