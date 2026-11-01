/* Order confirmation and order tracking pages. */
(function () {
  "use strict";
  var S = window.Store, U = window.UI, O = window.OrderUI, esc = S.escape;
  var $ = function (id) { return document.getElementById(id); };

  function initSuccess(root) {
    var o = S.getOrder(new URLSearchParams(location.search).get("order"));
    if (!o) {
      root.innerHTML = '<div class="empty">' + U.icon("box", 48) + "<h2>We couldn’t find that order</h2><p>Check your link, or use our order tracking page.</p>" +
        '<a class="btn btn--primary" href="track-order.html">Track Order</a></div>';
      return;
    }
    var first = esc(o.customer.name.split(" ")[0]);
    root.innerHTML = '<div class="confirm"><div class="success"><div class="success__icon">' + U.icon("check", 32) + "</div>" +
      "<h2>Thank you, " + first + "!</h2><p>Your order number is <strong>" + esc(o.number) + "</strong>. Keep it to track your delivery.</p>" +
      '<p class="notice">' + U.icon("info", 18) + "<span>This is a demo store: the order is saved in this browser only. It was not sent to the shop and nothing will be delivered.</span></p>" +
      '<div class="hero__cta"><a class="btn btn--primary" href="track-order.html?order=' + encodeURIComponent(o.number) + '">Track Order</a><a class="btn btn--outline" href="shop.html">Continue Shopping</a></div></div>' +
      '<div class="confirm__grid"><section class="panel"><h2>Items</h2>' + O.items(o) + "</section>" +
      '<section class="summary" aria-label="Order total"><h2>Summary</h2>' + O.totals(o) + '<p class="sum-note">Payment: Cash on Delivery</p>' +
      '<h3 class="summary__h">Delivering to</h3>' + O.address(o.customer) + "</section></div></div>";
  }

  function initTrack(form) {
    var msg = $("formMsg"), number = $("trackNumber"), phone = $("trackPhone"), result = $("trackResult");
    var preset = new URLSearchParams(location.search).get("order");
    if (preset) number.value = preset;
    form.addEventListener("input", function (e) { if (e.target.getAttribute("aria-invalid") === "true") U.fieldError(e.target, ""); });
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      msg.hidden = true; result.innerHTML = "";
      var okNumber = U.fieldError(number, number.value.trim() ? "" : "Enter your order number.");
      var okPhone = U.fieldError(phone, S.isPhone(phone.value) ? "" : "Enter a valid Bangladeshi mobile number.");
      if (!okNumber) { number.focus(); return; }
      if (!okPhone) { phone.focus(); return; }
      var o = S.trackOrder(number.value, phone.value);
      if (!o) { msg.textContent = "We couldn’t find an order with those details. Check them and try again."; msg.hidden = false; return; }
      result.innerHTML = '<section class="panel"><div class="order-card__head"><div><h2>' + esc(o.number) + "</h2><small>Placed " + esc(O.date(o.createdAt)) + "</small></div>" + O.pill(o.status) + "</div>" +
        O.timeline(o) + "<h3>Items</h3>" + O.items(o) + O.totals(o) + "</section>";
      result.focus();
    });
  }

  document.addEventListener("ashfi:ready", function () {
    var success = $("successRoot"), track = $("trackForm");
    if (success) initSuccess(success);
    if (track) initTrack(track);
  });
})();
