/* Cart page. Reads/writes via Store (localStorage). */
(function () {
  "use strict";
  var S = window.Store, D = S.data, U = window.UI, esc = S.escape;

  function line(l) {
    var p = S.getProduct(l.id), unit = S.unitPrice(p);
    var meta = [l.color, l.size && l.size !== "Free Size" ? "Size " + l.size : (l.size || "")].filter(Boolean).join(" · ");
    var k = esc(l.key);
    return '<li class="cart-line">' +
      '<a class="cart-line__img" href="product.html?id=' + encodeURIComponent(p.id) + '"><span class="img-wrap"><img src="' + p.images[0] + '" alt="' + esc(p.name) + '" width="600" height="800" loading="lazy"></span></a>' +
      "<div><h3 class=\"cart-line__name\"><a href=\"product.html?id=" + encodeURIComponent(p.id) + '">' + esc(p.name) + "</a></h3>" +
        '<p class="cart-line__meta">' + esc(meta) + "</p>" + U.priceBlock(p) + "</div>" +
      '<div class="cart-line__row">' +
        '<div class="qty" data-key="' + k + '"><button type="button" class="qty__btn" data-dec aria-label="Decrease quantity of ' + esc(p.name) + '">' + U.icon("minus", 16) + "</button>" +
          '<input class="qty__input" type="text" inputmode="numeric" value="' + l.qty + '" maxlength="2" aria-label="Quantity of ' + esc(p.name) + '">' +
          '<button type="button" class="qty__btn" data-inc aria-label="Increase quantity of ' + esc(p.name) + '">' + U.icon("plus", 16) + "</button></div>" +
        '<span class="cart-line__total" aria-label="Line total">' + S.money(unit * l.qty) + "</span>" +
        '<button type="button" class="link-btn" data-remove="' + k + '">' + U.icon("trash", 16) + " Remove</button></div></li>";
  }

  function render() {
    var root = document.getElementById("cartRoot"), cart = S.getCart();
    if (!cart.length) {
      root.innerHTML = '<div class="empty">' + U.icon("bag", 48) + "<h2>Your cart is empty</h2>" +
        "<p>Looks like you haven’t added anything yet. Explore our latest pieces and find something you love.</p>" +
        '<a class="btn btn--primary" href="shop.html">Start Shopping</a></div>';
      return;
    }
    var t = S.totals(), sub = t.subtotal, free = D.delivery.freeOver, left = Math.max(0, free - sub);
    var pct = Math.min(100, Math.round(sub / free * 100));
    root.innerHTML = '<div class="cart"><section aria-label="Cart items"><ul class="cart-list">' + cart.map(line).join("") + "</ul>" +
      '<p style="margin-top:20px"><a class="link-arrow" href="shop.html">' + U.icon("left", 16) + " Continue shopping</a></p></section>" +
      '<aside class="summary" aria-label="Order summary"><h2>Order Summary</h2>' +
        '<div class="progress"><div class="progress__bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' + pct + '" aria-label="Progress to free delivery"><i style="width:' + pct + '%"></i></div>' +
          "<p>" + (left ? "Add " + S.money(left) + " more for <strong>free delivery</strong>." : "🎉 You’ve unlocked <strong>free delivery</strong>!") + "</p></div>" +
        '<div class="sum-row"><span>Subtotal (' + S.cartCount() + " item" + (S.cartCount() === 1 ? "" : "s") + ")</span><span>" + S.money(sub) + "</span></div>" +
        (t.discount ? '<div class="sum-row sum-row--save"><span>Coupon discount</span><span>−' + S.money(t.discount) + "</span></div>" : "") +
        '<div class="sum-row"><span>Delivery</span><span>' + (t.coupon && t.coupon.type === "shipping" && !t.couponMessage ? "Free" : "Calculated at checkout") + "</span></div>" +
        '<div class="sum-row sum-row--total"><span>Total</span><span>' + S.money(t.total) + "</span></div>" +
        '<div class="coupon-box">' + U.couponMarkup(t) + "</div>" +
        '<p class="sum-note">Delivery: ' + S.money(D.delivery.insideDhaka) + " inside Dhaka, " + S.money(D.delivery.outsideDhaka) + " outside Dhaka.</p>" +
        '<a class="btn btn--primary btn--block" href="checkout.html">Proceed to Checkout</a></aside></div>';
    U.hydrate(root);
  }

  function init() {
    render();
    var root = document.getElementById("cartRoot");
    U.bindCoupon(root);
    root.addEventListener("click", function (e) {
      var rm = e.target.closest("[data-remove]");
      if (rm) { S.removeLine(rm.getAttribute("data-remove")); U.toast("Item removed from cart"); return; }
      var q = e.target.closest(".qty[data-key]"); if (!q) return;
      var input = q.querySelector(".qty__input"), v = parseInt(input.value, 10) || 1;
      if (e.target.closest("[data-inc]")) S.setQty(q.getAttribute("data-key"), v + 1);
      if (e.target.closest("[data-dec]")) S.setQty(q.getAttribute("data-key"), v - 1);
    });
    root.addEventListener("change", function (e) {
      var q = e.target.closest(".qty[data-key]");
      if (q && e.target.matches(".qty__input")) S.setQty(q.getAttribute("data-key"), parseInt(e.target.value, 10) || 1);
    });
    document.addEventListener("ashfi:cart", function () {
      var active = document.activeElement, key = active && active.closest && active.closest(".qty[data-key]");
      var which = active && active.hasAttribute && (active.hasAttribute("data-inc") ? "[data-inc]" : active.hasAttribute("data-dec") ? "[data-dec]" : "");
      render();
      if (key && which) { var again = document.querySelector('.qty[data-key="' + key.getAttribute("data-key").replace(/"/g, '\\"') + '"] ' + which); if (again) again.focus(); }
    });
  }
  document.addEventListener("ashfi:ready", init);
})();
