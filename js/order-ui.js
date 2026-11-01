/* Order building blocks shared by the account, confirmation and tracking pages. */
(function () {
  "use strict";
  var S = window.Store, U = window.UI, esc = S.escape;
  var STEPS = [["placed", "Order placed"], ["confirmed", "Confirmed"], ["shipped", "Shipped"], ["delivered", "Delivered"]];
  var LABELS = { placed: "Placed", confirmed: "Confirmed", shipped: "Shipped", delivered: "Delivered", cancelled: "Cancelled" };

  function date(iso, withTime) {
    var o = { day: "numeric", month: "short", year: "numeric" };
    if (withTime) { o.hour = "numeric"; o.minute = "2-digit"; }
    return new Date(iso).toLocaleString("en-GB", o);
  }

  function pill(status) { return '<span class="pill pill--' + esc(status) + '">' + esc(LABELS[status] || status) + "</span>"; }

  function timeline(o) {
    if (o.status === "cancelled") {
      return '<p class="notice">' + U.icon("info", 18) + "<span>This order was cancelled on " + esc(date(o.history[o.history.length - 1].at)) + ".</span></p>";
    }
    var reached = STEPS.map(function (s) { return s[0]; }).indexOf(o.status);
    return '<ol class="steps">' + STEPS.map(function (s, i) {
      var h = o.history.filter(function (x) { return x.status === s[0]; })[0];
      var cls = i < reached ? " is-done" : i === reached ? " is-done is-current" : "";
      return '<li class="steps__item' + cls + '"' + (i === reached ? ' aria-current="step"' : "") + '><span class="steps__dot">' + (i <= reached ? U.icon("check", 14) : "") + "</span>" +
        "<strong>" + esc(s[1]) + "</strong>" + (h ? "<small>" + esc(date(h.at)) + "</small>" : "") + "</li>";
    }).join("") + "</ol>";
  }

  function items(o) {
    return '<div class="mini-lines">' + o.items.map(function (i) {
      var p = S.getProduct(i.product_id);
      var name = p ? '<a href="product.html?id=' + encodeURIComponent(p.id) + '">' + esc(i.name) + "</a>" : esc(i.name);
      return '<div class="mini-line"><img src="' + esc(i.image) + '" alt="" width="56" height="74" loading="lazy"><div><strong>' + name + "</strong><small>" +
        esc([i.color, i.size].filter(Boolean).join(" · ")) + " × " + i.quantity + "</small></div><span>" + S.money(i.unit_price * i.quantity) + "</span></div>";
    }).join("") + "</div>";
  }

  function totals(o) {
    return '<div class="sum-row"><span>Subtotal</span><span>' + S.money(o.subtotal) + "</span></div>" +
      (o.discount ? '<div class="sum-row sum-row--save"><span>Coupon (' + esc(o.coupon) + ")</span><span>−" + S.money(o.discount) + "</span></div>" : "") +
      '<div class="sum-row"><span>Delivery</span><span>' + (o.delivery_charge ? S.money(o.delivery_charge) : "Free") + "</span></div>" +
      '<div class="sum-row sum-row--total"><span>Total</span><span>' + S.money(o.total) + "</span></div>";
  }

  function address(c) {
    return '<address class="addr">' + esc(c.name) + "<br>" + esc(c.address) + "<br>" + esc(c.district) + "<br>" + esc(c.phone) + "</address>";
  }

  /** Compact card for order lists. */
  function card(o) {
    var thumbs = o.items.slice(0, 4).map(function (i) { return '<img src="' + esc(i.image) + '" alt="" width="48" height="64" loading="lazy">'; }).join("");
    var count = o.items.reduce(function (n, i) { return n + i.quantity; }, 0);
    return '<article class="order-card"><header class="order-card__head"><div><h3>' + esc(o.number) + "</h3><small>" + esc(date(o.createdAt)) + "</small></div>" + pill(o.status) + "</header>" +
      '<div class="order-card__thumbs">' + thumbs + "</div>" +
      '<footer class="order-card__foot"><span>' + count + " item" + (count === 1 ? "" : "s") + " · <strong>" + S.money(o.total) + "</strong></span>" +
      '<a class="btn btn--outline btn--sm" href="account-order.html?order=' + encodeURIComponent(o.number) + '" aria-label="View order ' + esc(o.number) + '">View Order</a></footer></article>';
  }

  window.OrderUI = { date: date, pill: pill, timeline: timeline, items: items, totals: totals, address: address, card: card };
})();
