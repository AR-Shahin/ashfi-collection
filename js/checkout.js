/* Checkout – static demo interface, ready for future backend integration.
 *
 * Backend hook: buildOrderPayload() returns the exact JSON a Laravel endpoint
 * (e.g. POST /api/orders) would need. The submit handler currently does NOT send
 * anything – see the TODO in submitOrder().
 */
(function () {
  "use strict";
  var S = window.Store, D = S.data, U = window.UI, esc = S.escape;
  var $ = function (id) { return document.getElementById(id); };

  function lines() {
    return S.getCart().map(function (l) {
      var p = S.getProduct(l.id), unit = S.unitPrice(p);
      return { key: l.key, product: p, size: l.size, color: l.color, qty: l.qty, unit: unit, total: unit * l.qty };
    });
  }

  function areaValue() { var r = document.querySelector('input[name="area"]:checked'); return r ? r.value : "inside"; }

  function totals() {
    var sub = S.cartSubtotal(), delivery = S.deliveryCharge(areaValue(), sub);
    return { subtotal: sub, delivery: delivery, total: sub + delivery };
  }

  function buildOrderPayload() {
    var f = $("checkoutForm"), t = totals();
    return {
      customer: { name: f.name.value.trim(), phone: f.phone.value.trim(), address: f.address.value.trim(), district: f.district.value, note: f.note.value.trim() },
      delivery_area: areaValue(),
      payment_method: "cash_on_delivery",
      items: lines().map(function (l) { return { product_id: l.product.id, name: l.product.name, size: l.size, color: l.color, quantity: l.qty, unit_price: l.unit }; }),
      subtotal: t.subtotal, delivery_charge: t.delivery, total: t.total
    };
  }
  window.AshfiCheckout = { getOrderPayload: buildOrderPayload };

  function summaryHtml() {
    var t = totals();
    return '<h2>Order Summary</h2><div class="mini-lines">' + lines().map(function (l) {
      return '<div class="mini-line"><img src="' + l.product.images[0] + '" alt="" width="56" height="74" loading="lazy"><div><strong>' + esc(l.product.name) + "</strong><small>" +
        esc([l.color, l.size].filter(Boolean).join(" · ")) + " × " + l.qty + "</small></div><span>" + S.money(l.total) + "</span></div>";
    }).join("") + "</div>" +
      '<div class="sum-row"><span>Subtotal</span><span>' + S.money(t.subtotal) + "</span></div>" +
      '<div class="sum-row"><span>Delivery charge</span><span>' + (t.delivery === 0 ? "Free" : S.money(t.delivery)) + "</span></div>" +
      '<div class="sum-row sum-row--total"><span>Total</span><span>' + S.money(t.total) + "</span></div>" +
      '<p class="sum-note">Payment: Cash on Delivery</p>';
  }

  function refreshSummary() { $("checkoutSummary").innerHTML = summaryHtml(); }

  function setError(field, msg) {
    var el = $(field.id + "Err");
    field.setAttribute("aria-invalid", msg ? "true" : "false");
    if (el) el.textContent = msg || "";
    return !msg;
  }

  function validate() {
    var f = $("checkoutForm"), ok = true, first = null;
    var check = function (field, msg) { var good = setError(field, msg); if (!good && !first) first = field; ok = ok && good; };
    check(f.name, f.name.value.trim().length < 2 ? "Please enter your full name." : "");
    var phone = f.phone.value.replace(/[\s-]/g, "").replace(/^(\+?88)/, "");
    check(f.phone, /^01[3-9]\d{8}$/.test(phone) ? "" : "Enter a valid Bangladeshi mobile number (e.g. 01712345678).");
    check(f.address, f.address.value.trim().length < 10 ? "Please enter your full address (house, road, area)." : "");
    check(f.district, f.district.value ? "" : "Please select your district.");
    if (first) first.focus();
    return ok;
  }

  function submitOrder(e) {
    e.preventDefault();
    if (!validate()) return;
    var payload = buildOrderPayload();
    // TODO (backend): send `payload` to your server, e.g.
    //   fetch('/api/orders', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
    // Nothing is transmitted in this static demo.
    var ref = "DEMO-" + Date.now().toString(36).toUpperCase().slice(-6);
    S.clearCart();
    $("checkoutRoot").innerHTML = '<div class="success"><div class="success__icon">' + U.icon("check", 32) + "</div>" +
      "<h2>Thank you, " + esc(payload.customer.name.split(" ")[0]) + "!</h2>" +
      "<p>This is a <strong>demo confirmation</strong> – no order was placed and nothing was sent or charged.</p>" +
      "<p>Reference: <strong>" + ref + "</strong><br>Total: <strong>" + S.money(payload.total) + "</strong> (Cash on Delivery)</p>" +
      '<div class="hero__cta"><a class="btn btn--primary" href="shop.html">Continue Shopping</a><a class="btn btn--outline" href="index.html">Back to Home</a></div></div>';
    window.scrollTo({ top: 0, behavior: "smooth" });
  }

  function init() {
    var root = $("checkoutRoot");
    if (!S.getCart().length) {
      root.innerHTML = '<div class="empty">' + U.icon("bag", 48) + "<h2>Your cart is empty</h2><p>Add something beautiful to your cart before checking out.</p>" +
        '<a class="btn btn--primary" href="shop.html">Browse the Shop</a></div>';
      return;
    }
    var districts = D.districts.map(function (d) { return "<option>" + esc(d) + "</option>"; }).join("");
    root.innerHTML = '<div class="checkout"><form id="checkoutForm" novalidate>' +
      '<p class="notice">' + U.icon("info", 18) + "<span>Demo checkout: no order is submitted and no payment is taken.</span></p>" +
      '<section class="panel"><h2>Delivery Details</h2><div class="form-grid">' +
        '<div class="form-grid form-grid--2"><div class="field"><label for="name">Full name</label><input id="name" name="name" autocomplete="name" required><p class="field__error" id="nameErr"></p></div>' +
        '<div class="field"><label for="phone">Phone number</label><input id="phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="01XXXXXXXXX" required><p class="field__error" id="phoneErr"></p></div></div>' +
        '<div class="field"><label for="address">Full address</label><textarea id="address" name="address" autocomplete="street-address" placeholder="House, road, area" required></textarea><p class="field__error" id="addressErr"></p></div>' +
        '<div class="form-grid form-grid--2"><div class="field"><label for="district">District</label><select id="district" name="district" required><option value="">Select district</option>' + districts + '</select><p class="field__error" id="districtErr"></p></div>' +
        '<div class="field"><span class="label" id="areaLabel">Delivery area</span><div role="radiogroup" aria-labelledby="areaLabel">' +
          '<label class="f-opt"><input type="radio" name="area" value="inside" checked> Inside Dhaka <small>' + S.money(D.delivery.insideDhaka) + "</small></label>" +
          '<label class="f-opt"><input type="radio" name="area" value="outside"> Outside Dhaka <small>' + S.money(D.delivery.outsideDhaka) + "</small></label></div></div></div>" +
        '<div class="field"><label for="note">Order note <small>(optional)</small></label><textarea id="note" name="note" placeholder="Anything we should know?"></textarea></div>' +
      "</div></section>" +
      '<section class="panel"><h2>Payment Method</h2>' +
        '<label class="pay-opt"><input type="radio" name="pay" checked><span><strong>Cash on Delivery</strong><span>Pay in cash when your order arrives.</span></span></label>' +
        '<label class="pay-opt pay-opt--off"><input type="radio" name="pay" disabled><span><strong>Online payment</strong><span>bKash, Nagad and cards – coming soon.</span></span></label></section>' +
      '<button type="submit" class="btn btn--primary btn--block" style="min-height:52px">Place Order (Demo)</button></form>' +
      '<aside class="summary" id="checkoutSummary" aria-label="Order summary"></aside></div>';

    refreshSummary();
    var form = $("checkoutForm");
    form.addEventListener("submit", submitOrder);
    form.addEventListener("change", function (e) {
      if (e.target.name === "area") refreshSummary();
      if (e.target.name === "district" && e.target.value) {
        var inside = e.target.value === "Dhaka";
        form.querySelector('input[name="area"][value="' + (inside ? "inside" : "outside") + '"]').checked = true;
        refreshSummary();
      }
    });
    form.addEventListener("input", function (e) { if (e.target.getAttribute("aria-invalid") === "true") setError(e.target, ""); });
  }
  document.addEventListener("ashfi:ready", init);
})();
