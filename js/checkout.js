/* Checkout – static demo interface, ready for future backend integration.
 *
 * Backend hook: buildOrderPayload() returns the exact JSON a Laravel endpoint
 * (e.g. POST /api/orders) would need. The submit handler currently only saves the
 * order in this browser – see the TODO in submitOrder().
 */
(function () {
  "use strict";
  var S = window.Store, D = S.data, U = window.UI, A = window.Auth, esc = S.escape;
  var setError = U.fieldError;
  var $ = function (id) { return document.getElementById(id); };

  function lines() {
    return S.getCart().map(function (l) {
      var p = S.getProduct(l.id), unit = S.unitPrice(p);
      return { key: l.key, product: p, size: l.size, color: l.color, qty: l.qty, unit: unit, total: unit * l.qty };
    });
  }

  var placing = false; // the cart empties while an order is placed; don't redraw an empty summary

  function areaValue() { var r = document.querySelector('input[name="area"]:checked'); return r ? r.value : "inside"; }

  function totals() { return S.totals(areaValue()); }

  function buildOrderPayload() {
    var f = $("checkoutForm"), t = totals();
    return {
      customer: { name: f.name.value.trim(), phone: f.phone.value.trim(), address: f.address.value.trim(), district: f.district.value, note: f.note.value.trim() },
      delivery_area: areaValue(),
      payment_method: "cash_on_delivery",
      items: lines().map(function (l) { return { product_id: l.product.id, name: l.product.name, image: l.product.images[0], size: l.size, color: l.color, quantity: l.qty, unit_price: l.unit }; }),
      subtotal: t.subtotal, coupon: t.discount || (t.coupon && !t.couponMessage) ? t.coupon.code : null, discount: t.discount, delivery_charge: t.delivery, total: t.total
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
      (t.discount ? '<div class="sum-row sum-row--save"><span>Coupon discount</span><span>−' + S.money(t.discount) + "</span></div>" : "") +
      '<div class="sum-row"><span>Delivery charge</span><span>' + (t.delivery === 0 ? "Free" : S.money(t.delivery)) + "</span></div>" +
      '<div class="sum-row sum-row--total"><span>Total</span><span>' + S.money(t.total) + "</span></div>" +
      '<div class="coupon-box">' + U.couponMarkup(t) + "</div>" +
      '<p class="sum-note">Payment: Cash on Delivery</p>';
  }

  function refreshSummary() { $("checkoutSummary").innerHTML = summaryHtml(); }

  function validate() {
    var f = $("checkoutForm"), ok = true, first = null;
    var check = function (field, msg) { var good = setError(field, msg); if (!good && !first) first = field; ok = ok && good; };
    check(f.name, f.name.value.trim().length < 2 ? "Please enter your full name." : "");
    check(f.phone, S.isPhone(f.phone.value) ? "" : "Enter a valid Bangladeshi mobile number (e.g. 01712345678).");
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
    // Nothing is transmitted in this static demo; the order stays in this browser.
    var save = $("saveAddress");
    if (save && save.checked) {
      A.saveAddress({ label: "Home", name: payload.customer.name, phone: S.normalizePhone(payload.customer.phone), address: payload.customer.address, district: payload.customer.district });
    }
    placing = true;
    var order = S.saveOrder(payload);
    S.clearCart();
    location.href = "order-success.html?order=" + encodeURIComponent(order.number);
  }

  function setArea(district) {
    $("checkoutForm").querySelector('input[name="area"][value="' + (district === "Dhaka" ? "inside" : "outside") + '"]').checked = true;
    refreshSummary();
  }

  function fillAddress(a) {
    var f = $("checkoutForm");
    f.name.value = a.name; f.phone.value = a.phone; f.address.value = a.address; f.district.value = a.district;
    setArea(a.district);
  }

  function savedAddressField(user) {
    if (!user || !user.addresses.length) return "";
    var options = user.addresses.map(function (a) {
      return '<option value="' + esc(a.id) + '"' + (a.isDefault ? " selected" : "") + ">" + esc(a.label + " – " + a.address + ", " + a.district) + "</option>";
    }).join("");
    return '<div class="field"><label for="savedAddress">Saved address</label><select id="savedAddress">' + options + '<option value="">Use a new address</option></select></div>';
  }

  function init() {
    var root = $("checkoutRoot");
    if (!S.getCart().length) {
      root.innerHTML = '<div class="empty">' + U.icon("bag", 48) + "<h2>Your cart is empty</h2><p>Add something beautiful to your cart before checking out.</p>" +
        '<a class="btn btn--primary" href="shop.html">Browse the Shop</a></div>';
      return;
    }
    var districts = D.districts.map(function (d) { return "<option>" + esc(d) + "</option>"; }).join("");
    var user = A.current();
    root.innerHTML = '<div class="checkout"><form id="checkoutForm" novalidate>' +
      '<p class="notice">' + U.icon("info", 18) + "<span>Demo checkout: no order is sent to the shop and no payment is taken.</span></p>" +
      (user ? "" : '<p class="notice">' + U.icon("user", 18) + '<span>Returning customer? <a class="text-link" href="login.html?next=checkout.html">Log in</a> for faster checkout, or continue as a guest.</span></p>') +
      '<section class="panel"><h2>Delivery Details</h2><div class="form-grid">' + savedAddressField(user) +
        '<div class="form-grid form-grid--2"><div class="field"><label for="name">Full name</label><input id="name" name="name" autocomplete="name" required><p class="field__error" id="nameErr"></p></div>' +
        '<div class="field"><label for="phone">Phone number</label><input id="phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="01XXXXXXXXX" required><p class="field__error" id="phoneErr"></p></div></div>' +
        '<div class="field"><label for="address">Full address</label><textarea id="address" name="address" autocomplete="street-address" placeholder="House, road, area" required></textarea><p class="field__error" id="addressErr"></p></div>' +
        '<div class="form-grid form-grid--2"><div class="field"><label for="district">District</label><select id="district" name="district" required><option value="">Select district</option>' + districts + '</select><p class="field__error" id="districtErr"></p></div>' +
        '<div class="field"><span class="label" id="areaLabel">Delivery area</span><div role="radiogroup" aria-labelledby="areaLabel">' +
          '<label class="f-opt"><input type="radio" name="area" value="inside" checked> Inside Dhaka <small>' + S.money(D.delivery.insideDhaka) + "</small></label>" +
          '<label class="f-opt"><input type="radio" name="area" value="outside"> Outside Dhaka <small>' + S.money(D.delivery.outsideDhaka) + "</small></label></div></div></div>" +
        '<div class="field"><label for="note">Order note <small>(optional)</small></label><textarea id="note" name="note" placeholder="Anything we should know?"></textarea></div>' +
        (user ? '<label class="f-opt" id="saveAddressRow"' + (user.addresses.length ? " hidden" : "") + '><input type="checkbox" id="saveAddress"> Save this address to my account</label>' : "") +
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
      if (e.target.name === "district" && e.target.value) setArea(e.target.value);
      if (e.target.id === "savedAddress") {
        var picked = user.addresses.filter(function (a) { return a.id === e.target.value; })[0];
        $("saveAddressRow").hidden = !!picked;
        if (!picked) $("saveAddress").checked = false;
        fillAddress(picked || { name: user.name, phone: user.phone, address: "", district: "" });
      }
    });
    form.addEventListener("input", function (e) { if (e.target.getAttribute("aria-invalid") === "true") setError(e.target, ""); });
    U.bindCoupon($("checkoutSummary"));
    document.addEventListener("ashfi:cart", function () { if (!placing) refreshSummary(); });
    if (user) {
      var address = A.defaultAddress();
      if (address) fillAddress(address);
      else { form.name.value = user.name; form.phone.value = user.phone; }
    }
  }
  document.addEventListener("ashfi:ready", init);
})();
