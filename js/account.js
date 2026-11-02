/* Customer account pages. Requires a signed-in customer; see Auth in store.js. */
(function () {
  "use strict";
  var S = window.Store, D = S.data, U = window.UI, A = window.Auth, O = window.OrderUI, esc = S.escape;
  var $ = function (id) { return document.getElementById(id); };
  var root;

  /* ---------------------------------------------------------- dashboard */
  function dashboard(user) {
    var orders = S.myOrders(), active = orders.filter(function (o) { return ["placed", "confirmed", "shipped"].indexOf(o.status) !== -1; });
    var address = A.defaultAddress();
    var stat = function (href, n, label) { return '<a class="stat" href="' + href + '"><strong>' + n + "</strong><span>" + label + "</span></a>"; };
    root.innerHTML = '<div class="stats account__stats">' +
        stat("account-orders.html", orders.length, "Total orders") + stat("account-orders.html", active.length, "In progress") +
        stat("wishlist.html", S.getWishlist().length, "Wishlist") + stat("account-addresses.html", user.addresses.length, "Addresses") + "</div>" +
      '<section class="panel"><div class="panel__head"><h2>Recent Orders</h2>' + (orders.length ? '<a class="link-arrow" href="account-orders.html">View all ' + U.icon("right", 16) + "</a>" : "") + "</div>" +
        (orders.length ? '<div class="order-list">' + orders.slice(0, 3).map(O.card).join("") + "</div>" :
          '<div class="empty empty--sm">' + U.icon("box", 40) + "<p>You haven’t placed an order yet.</p>" + '<a class="btn btn--primary btn--sm" href="shop.html">Start Shopping</a></div>') + "</section>" +
      '<div class="account__cols"><section class="panel"><div class="panel__head"><h2>Default Address</h2><a class="link-arrow" href="account-addresses.html">Manage ' + U.icon("right", 16) + "</a></div>" +
        (address ? O.address(address) : "<p>No address saved yet.</p>") + "</section>" +
      '<section class="panel"><div class="panel__head"><h2>Your Details</h2><a class="link-arrow" href="account-profile.html">Edit ' + U.icon("right", 16) + "</a></div>" +
        '<address class="addr">' + esc(user.name) + "<br>" + esc(user.email) + "<br>" + esc(user.phone) + "</address></section></div>";
  }

  /* ------------------------------------------------------------- orders */
  var GROUPS = { all: null, active: ["placed", "confirmed", "shipped"], delivered: ["delivered"], cancelled: ["cancelled"] };
  function ordersPage() {
    var current = "all";
    function draw() {
      var all = S.myOrders(), list = all.filter(function (o) { return !GROUPS[current] || GROUPS[current].indexOf(o.status) !== -1; });
      var tab = function (k, label) { return '<button type="button" class="tab" data-tab="' + k + '" aria-pressed="' + (k === current) + '">' + label + "</button>"; };
      root.innerHTML = '<div class="tabs" role="group" aria-label="Filter orders">' + tab("all", "All") + tab("active", "In Progress") + tab("delivered", "Delivered") + tab("cancelled", "Cancelled") + "</div>" +
        (list.length ? '<div class="order-list">' + list.map(O.card).join("") + "</div>" :
          '<div class="empty">' + U.icon("box", 44) + "<h2>No orders here</h2>" + (all.length ? "<p>Try a different filter.</p>" : '<p>When you place an order it will appear here.</p><a class="btn btn--primary" href="shop.html">Start Shopping</a>') + "</div>");
    }
    root.addEventListener("click", function (e) {
      var t = e.target.closest("[data-tab]"); if (!t) return;
      current = t.getAttribute("data-tab"); draw();
      root.querySelector('[data-tab="' + current + '"]').focus();
    });
    draw();
  }

  function orderPage(user) {
    var number = new URLSearchParams(location.search).get("order");
    function draw() {
      var o = S.getOrder(number);
      if (!o || o.userId !== user.id) {
        root.innerHTML = '<div class="empty">' + U.icon("box", 44) + "<h2>Order not found</h2><p>This order isn’t on your account.</p>" +
          '<a class="btn btn--primary" href="account-orders.html">Back to My Orders</a></div>';
        return;
      }
      root.innerHTML = '<section class="panel"><div class="order-card__head"><div><h2>' + esc(o.number) + "</h2><small>Placed " + esc(O.date(o.createdAt, true)) + "</small></div>" + O.pill(o.status) + "</div>" + O.timeline(o) + "</section>" +
        '<section class="panel"><h2>Items</h2>' + O.items(o) + "</section>" +
        '<div class="account__cols"><section class="panel"><h2>Delivery Address</h2>' + O.address(o.customer) + (o.customer.note ? "<p><strong>Note:</strong> " + esc(o.customer.note) + "</p>" : "") + "</section>" +
        '<section class="panel"><h2>Payment</h2>' + O.totals(o) + '<p class="sum-note">Cash on Delivery</p></section></div>' +
        '<div class="account__actions"><button type="button" class="btn btn--primary" data-reorder>Order Again</button>' +
        (o.status === "placed" ? '<button type="button" class="btn btn--outline" data-cancel>Cancel Order</button>' : "") +
        '<a class="btn btn--outline" href="account-orders.html">Back to My Orders</a></div>';
    }
    root.addEventListener("click", function (e) {
      var o = S.getOrder(number); if (!o || o.userId !== user.id) return;
      if (e.target.closest("[data-reorder]")) {
        var n = S.reorder(o);
        if (n) U.toast("Items added to your cart", { href: "cart.html", linkText: "View cart" });
        else U.toast("These items are no longer available", { type: "error" });
      }
      if (e.target.closest("[data-cancel]") && window.confirm("Cancel order " + o.number + "? This can’t be undone.")) {
        S.cancelOrder(number); U.toast("Order cancelled"); draw();
      }
    });
    draw();
  }

  /* ---------------------------------------------------------- addresses */
  function addressesPage() {
    var editing = null;

    function formHtml(a) {
      var districts = D.districts.map(function (d) { return "<option" + (a.district === d ? " selected" : "") + ">" + esc(d) + "</option>"; }).join("");
      var input = function (id, label, value, attrs) {
        return '<div class="field"><label for="' + id + '">' + label + '</label><input id="' + id + '" value="' + esc(value || "") + '" ' + (attrs || "") + '><p class="field__error" id="' + id + 'Err"></p></div>';
      };
      return '<form class="panel form-grid" id="addrForm" novalidate><h2>' + (a.id ? "Edit Address" : "Add a New Address") + "</h2>" +
        '<div class="form-grid form-grid--2">' + input("adrLabel", "Label", a.label, 'placeholder="Home, Office…" maxlength="30"') + input("adrName", "Full name", a.name, 'autocomplete="name"') + "</div>" +
        '<div class="form-grid form-grid--2">' + input("adrPhone", "Mobile number", a.phone, 'type="tel" inputmode="tel" autocomplete="tel" placeholder="01XXXXXXXXX"') +
          '<div class="field"><label for="adrDistrict">District</label><select id="adrDistrict"><option value="">Select district</option>' + districts + '</select><p class="field__error" id="adrDistrictErr"></p></div></div>' +
        '<div class="field"><label for="adrAddress">Full address</label><textarea id="adrAddress" autocomplete="street-address" placeholder="House, road, area">' + esc(a.address || "") + '</textarea><p class="field__error" id="adrAddressErr"></p></div>' +
        '<label class="f-opt"><input type="checkbox" id="adrDefault"' + (a.isDefault ? " checked" : "") + "> Make this my default address</label>" +
        '<div class="account__actions"><button type="submit" class="btn btn--primary">Save Address</button><button type="button" class="btn btn--outline" data-addr-cancel>Cancel</button></div></form>';
    }

    function draw() {
      var user = A.current();
      var cards = user.addresses.map(function (a) {
        return '<article class="addr-card' + (a.isDefault ? " is-default" : "") + '"><header><h3>' + esc(a.label) + "</h3>" + (a.isDefault ? '<span class="pill">Default</span>' : "") + "</header>" +
          O.address(a) + '<footer><button type="button" class="link-btn" data-addr-edit="' + esc(a.id) + '">' + U.icon("edit", 16) + " Edit</button>" +
          (a.isDefault ? "" : '<button type="button" class="link-btn" data-addr-default="' + esc(a.id) + '">Set as default</button>') +
          '<button type="button" class="link-btn" data-addr-delete="' + esc(a.id) + '">' + U.icon("trash", 16) + " Delete</button></footer></article>";
      }).join("");
      root.innerHTML = (editing ? formHtml(editing) : '<div class="account__actions"><button type="button" class="btn btn--primary" data-addr-add>' + U.icon("plus", 16) + " Add Address</button></div>") +
        (cards ? '<div class="addr-grid">' + cards + "</div>" : editing ? "" : '<div class="empty">' + U.icon("pin", 44) + "<h2>No saved addresses</h2><p>Add one to speed up checkout.</p></div>");
      var form = $("addrForm");
      if (form) { form.addEventListener("submit", save); $("adrLabel").focus(); }
    }

    function save(e) {
      e.preventDefault();
      var label = $("adrLabel"), name = $("adrName"), phone = $("adrPhone"), district = $("adrDistrict"), address = $("adrAddress");
      var checks = [[label, label.value.trim() ? "" : "Add a label, such as Home."], [name, name.value.trim().length < 2 ? "Please enter a full name." : ""],
        [phone, S.isPhone(phone.value) ? "" : "Enter a valid Bangladeshi mobile number."], [district, district.value ? "" : "Please select a district."],
        [address, address.value.trim().length < 10 ? "Please enter the full address (house, road, area)." : ""]];
      var first = null;
      checks.forEach(function (c) { if (!U.fieldError(c[0], c[1]) && !first) first = c[0]; });
      if (first) { first.focus(); return; }
      A.saveAddress({ id: editing.id, label: label.value.trim(), name: name.value.trim(), phone: S.normalizePhone(phone.value), district: district.value, address: address.value.trim(), isDefault: $("adrDefault").checked });
      editing = null; U.toast("Address saved"); draw();
    }

    root.addEventListener("click", function (e) {
      var t = e.target.closest("[data-addr-add],[data-addr-cancel],[data-addr-edit],[data-addr-default],[data-addr-delete]");
      if (!t) return;
      var user = A.current(), find = function (id) { return user.addresses.filter(function (a) { return a.id === id; })[0]; };
      if (t.hasAttribute("data-addr-add")) { editing = { label: "", name: user.name, phone: user.phone }; draw(); }
      if (t.hasAttribute("data-addr-cancel")) { editing = null; draw(); }
      if (t.hasAttribute("data-addr-edit")) { editing = find(t.getAttribute("data-addr-edit")); draw(); }
      if (t.hasAttribute("data-addr-default")) { A.setDefaultAddress(t.getAttribute("data-addr-default")); U.toast("Default address updated"); draw(); }
      if (t.hasAttribute("data-addr-delete") && window.confirm("Delete this address?")) { A.deleteAddress(t.getAttribute("data-addr-delete")); U.toast("Address deleted"); draw(); }
    });
    draw();
  }

  /* ------------------------------------------------------------ profile */
  function profilePage(user) {
    var pwField = function (id, label, ac, hint) {
      return '<div class="field"><label for="' + id + '">' + label + '</label><div class="pw"><input id="' + id + '" type="password" autocomplete="' + ac + '">' +
        '<button type="button" class="pw__toggle" data-pw-toggle="' + id + '" aria-pressed="false" aria-label="Show or hide password">Show</button></div><p class="field__error" id="' + id + 'Err"></p>' + (hint || "") + "</div>";
    };
    var input = function (id, label, value, attrs) {
      return '<div class="field"><label for="' + id + '">' + label + '</label><input id="' + id + '" value="' + esc(value) + '" ' + attrs + '><p class="field__error" id="' + id + 'Err"></p></div>';
    };
    root.innerHTML = '<form class="panel form-grid" id="profileForm" novalidate><h2>Personal Details</h2>' +
        input("pfName", "Full name", user.name, 'autocomplete="name"') +
        '<div class="form-grid form-grid--2">' + input("pfEmail", "Email address", user.email, 'type="email" inputmode="email" autocomplete="email"') +
        input("pfPhone", "Mobile number", user.phone, 'type="tel" inputmode="tel" autocomplete="tel"') + "</div>" +
        '<div><button type="submit" class="btn btn--primary">Save Changes</button></div></form>' +
      '<form class="panel form-grid" id="passwordForm" novalidate><h2>Change Password</h2>' +
        pwField("pwCurrent", "Current password", "current-password") +
        '<div class="form-grid form-grid--2">' + pwField("pwNew", "New password", "new-password", '<p class="field__hint">At least 8 characters.</p>') + pwField("pwConfirm", "Confirm new password", "new-password") + "</div>" +
        '<div><button type="submit" class="btn btn--primary">Update Password</button></div></form>';

    var run = function (checks) {
      var first = null;
      checks.forEach(function (c) { if (!U.fieldError(c[0], c[1]) && !first) first = c[0]; });
      if (first) first.focus();
      return !first;
    };
    $("profileForm").addEventListener("submit", function (e) {
      e.preventDefault();
      var name = $("pfName"), email = $("pfEmail"), phone = $("pfPhone");
      if (!run([[name, name.value.trim().length < 2 ? "Please enter your full name." : ""], [email, S.isEmail(email.value) ? "" : "Enter a valid email address."],
        [phone, S.isPhone(phone.value) ? "" : "Enter a valid Bangladeshi mobile number."]])) return;
      var res = A.updateProfile({ name: name.value, email: email.value, phone: phone.value });
      if (!res.ok) { U.fieldError(email, res.message); email.focus(); return; }
      $("accName").textContent = name.value.trim().split(" ")[0];
      U.toast("Details saved");
    });
    $("passwordForm").addEventListener("submit", function (e) {
      e.preventDefault();
      var form = e.target, cur = $("pwCurrent"), next = $("pwNew"), confirm = $("pwConfirm");
      if (!run([[cur, cur.value ? "" : "Enter your current password."], [next, next.value.length < 8 ? "Use at least 8 characters." : ""], [confirm, confirm.value !== next.value ? "Passwords don’t match." : ""]])) return;
      A.changePassword(cur.value, next.value).then(function (res) {
        if (res.ok) { form.reset(); U.toast("Password updated"); return; }
        if (res.field) { U.fieldError(cur, res.message); cur.focus(); } else U.toast(res.message, { type: "error" });
      });
    });
    root.addEventListener("input", function (e) { if (e.target.getAttribute("aria-invalid") === "true") U.fieldError(e.target, ""); });
  }

  document.addEventListener("ashfi:ready", function () {
    var user = A.current();
    if (!user) {
      var here = location.pathname.split("/").pop() + location.search;
      location.replace("login.html?next=" + encodeURIComponent(here));
      return;
    }
    root = $("accountRoot");
    $("accName").textContent = user.name.split(" ")[0];
    document.querySelector("[data-logout]").addEventListener("click", function () { A.logout(); location.href = "index.html"; });
    var page = document.body.getAttribute("data-page");
    if (page === "account") dashboard(user);
    if (page === "account-orders") ordersPage();
    if (page === "account-order") orderPage(user);
    if (page === "account-addresses") addressesPage();
    if (page === "account-profile") profilePage(user);
    U.hydrate(root);
    // on phones the menu scrolls sideways; bring the current section into view
    var on = document.querySelector('.account__nav [aria-current="page"]');
    if (on) { var list = on.closest("ul"); list.scrollLeft += on.getBoundingClientRect().left - list.getBoundingClientRect().left; }
  });
})();
