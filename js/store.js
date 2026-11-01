/**
 * Ashfi Collection – shared state & helpers (cart, wishlist, coupons, orders,
 * customer accounts, formatting).
 * Everything lives in this browser's localStorage. The storage layer is isolated
 * here so it can later be swapped for API calls (e.g. Laravel) in one place.
 *
 * Accounts here are a front-end DEMO: browser storage cannot protect anything,
 * so real logins, orders and coupon checks must move to the server before launch.
 */
(function () {
  "use strict";
  var D = window.ASHFI;
  var CART_KEY = "ashfi_cart_v1";
  var WISH_KEY = "ashfi_wishlist_v1";
  var COUPON_KEY = "ashfi_coupon_v1";
  var USERS_KEY = "ashfi_users_v1";
  var ORDERS_KEY = "ashfi_orders_v1";
  var SESSION_KEY = "ashfi_session_v1";
  var RESET_KEY = "ashfi_reset_v1";

  function read(key) {
    try { var v = JSON.parse(localStorage.getItem(key)); return Array.isArray(v) ? v : []; }
    catch (e) { return []; }
  }
  function write(key, val) {
    try { localStorage.setItem(key, JSON.stringify(val)); } catch (e) { /* storage unavailable */ }
  }

  var Store = {
    data: D,

    /* ---------- formatting ---------- */
    money: function (n) { return "৳" + Math.round(n).toLocaleString("en-US"); },
    unitPrice: function (p) { return p.salePrice && p.salePrice < p.price ? p.salePrice : p.price; },
    discountPct: function (p) {
      return p.salePrice && p.salePrice < p.price ? Math.round((1 - p.salePrice / p.price) * 100) : 0;
    },
    getProduct: function (id) { return D.products.filter(function (p) { return p.id === id; })[0] || null; },
    getCategory: function (slug) { return D.categories.filter(function (c) { return c.slug === slug; })[0] || null; },
    escape: function (s) {
      return String(s).replace(/[&<>"']/g, function (c) {
        return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
      });
    },
    emit: function (name) { document.dispatchEvent(new CustomEvent(name)); },

    /* ---------- cart ---------- */
    getCart: function () {
      // drop lines whose product no longer exists in data.js
      return read(CART_KEY).filter(function (l) { return Store.getProduct(l.id); });
    },
    cartCount: function () {
      return Store.getCart().reduce(function (n, l) { return n + l.qty; }, 0);
    },
    cartSubtotal: function () {
      return Store.getCart().reduce(function (sum, l) {
        return sum + Store.unitPrice(Store.getProduct(l.id)) * l.qty;
      }, 0);
    },
    addToCart: function (id, opts) {
      opts = opts || {};
      var p = Store.getProduct(id);
      if (!p) return false;
      var size = opts.size || (p.sizes.length === 1 ? p.sizes[0] : "");
      var color = opts.color || p.colors[0].name;
      var qty = Math.max(1, Math.min(10, parseInt(opts.qty, 10) || 1));
      var cart = read(CART_KEY);
      var key = [id, size, color].join("|");
      var line = cart.filter(function (l) { return l.key === key; })[0];
      if (line) line.qty = Math.min(10, line.qty + qty);
      else cart.push({ key: key, id: id, size: size, color: color, qty: qty });
      write(CART_KEY, cart);
      Store.emit("ashfi:cart");
      return true;
    },
    setQty: function (key, qty) {
      var cart = read(CART_KEY);
      cart = cart.map(function (l) { if (l.key === key) l.qty = Math.max(1, Math.min(10, qty)); return l; });
      write(CART_KEY, cart);
      Store.emit("ashfi:cart");
    },
    removeLine: function (key) {
      write(CART_KEY, read(CART_KEY).filter(function (l) { return l.key !== key; }));
      Store.emit("ashfi:cart");
    },
    clearCart: function () { write(CART_KEY, []); Store.emit("ashfi:cart"); },

    /* ---------- wishlist ---------- */
    getWishlist: function () {
      return read(WISH_KEY).filter(function (id) { return Store.getProduct(id); });
    },
    inWishlist: function (id) { return read(WISH_KEY).indexOf(id) !== -1; },
    toggleWishlist: function (id) {
      var list = read(WISH_KEY);
      var i = list.indexOf(id);
      if (i === -1) list.push(id); else list.splice(i, 1);
      write(WISH_KEY, list);
      Store.emit("ashfi:wishlist");
      return i === -1; // true when added
    },

    /* ---------- delivery ---------- */
    deliveryCharge: function (area, subtotal) {
      if (!subtotal) return 0;
      if (subtotal >= D.delivery.freeOver) return 0;
      return area === "outside" ? D.delivery.outsideDhaka : D.delivery.insideDhaka;
    },

    /* ---------- coupons ---------- */
    getCoupon: function (code) {
      code = String(code || "").trim().toUpperCase();
      return D.coupons.filter(function (c) { return c.code === code; })[0] || null;
    },
    isCouponExpired: function (c) { return !!c.expires && new Date(c.expires + "T23:59:59") < new Date(); },
    /** What the applied coupon is worth against this subtotal, and why if it is worth nothing. */
    couponState: function (subtotal) {
      var code = readText(COUPON_KEY), c = code && Store.getCoupon(code);
      if (!c || Store.isCouponExpired(c)) return { coupon: null, discount: 0, freeDelivery: false, message: "" };
      if (subtotal < c.minSpend) {
        return { coupon: c, discount: 0, freeDelivery: false, message: "Add " + Store.money(c.minSpend - subtotal) + " more to use " + c.code + "." };
      }
      var discount = 0;
      if (c.type === "percent") discount = Math.min(Math.round(subtotal * c.value / 100), c.maxDiscount || Infinity);
      if (c.type === "flat") discount = Math.min(c.value, subtotal);
      return { coupon: c, discount: discount, freeDelivery: c.type === "shipping", message: "" };
    },
    applyCoupon: function (code) {
      var c = Store.getCoupon(code), sub = Store.cartSubtotal();
      if (!c) return { ok: false, message: "That coupon code isn’t valid." };
      if (Store.isCouponExpired(c)) return { ok: false, message: "That coupon has expired." };
      if (sub < c.minSpend) return { ok: false, message: c.code + " needs a minimum spend of " + Store.money(c.minSpend) + "." };
      writeText(COUPON_KEY, c.code);
      Store.emit("ashfi:cart");
      return { ok: true, message: c.code + " applied." };
    },
    removeCoupon: function () { writeText(COUPON_KEY, ""); Store.emit("ashfi:cart"); },
    /** One source of truth for money. Pass no area while delivery is still unknown (cart page). */
    totals: function (area) {
      var sub = Store.cartSubtotal(), state = Store.couponState(sub);
      var delivery = area ? (state.freeDelivery ? 0 : Store.deliveryCharge(area, sub)) : null;
      return { subtotal: sub, discount: state.discount, delivery: delivery, total: sub - state.discount + (delivery || 0), coupon: state.coupon, couponMessage: state.message };
    },

    /* ---------- validation ---------- */
    normalizePhone: function (v) { return String(v || "").replace(/[\s-]/g, "").replace(/^(\+?88)/, ""); },
    isPhone: function (v) { return /^01[3-9]\d{8}$/.test(Store.normalizePhone(v)); },
    isEmail: function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(String(v || "").trim()); },

    /* ---------- orders ---------- */
    /* TODO (backend): POST the order to /api/orders and let the server price it. */
    saveOrder: function (o) {
      var orders = read(ORDERS_KEY), user = window.Auth.current(), now = new Date().toISOString();
      var order = Object.assign({}, o, {
        number: "AC-" + randomHex(4).toUpperCase(), userId: user ? user.id : null, status: "placed",
        createdAt: now, history: [{ status: "placed", at: now }]
      });
      orders.unshift(order);
      write(ORDERS_KEY, orders);
      writeText(COUPON_KEY, "");
      return order;
    },
    getOrder: function (number) { return read(ORDERS_KEY).filter(function (o) { return o.number === number; })[0] || null; },
    myOrders: function () {
      var user = window.Auth.current();
      return user ? read(ORDERS_KEY).filter(function (o) { return o.userId === user.id; }) : [];
    },
    /** Tracking needs the phone number too, so an order number alone reveals nothing. */
    trackOrder: function (number, phone) {
      var o = Store.getOrder(String(number || "").trim().toUpperCase());
      return o && Store.normalizePhone(o.customer.phone) === Store.normalizePhone(phone) ? o : null;
    },
    cancelOrder: function (number) {
      var orders = read(ORDERS_KEY), o = orders.filter(function (x) { return x.number === number; })[0];
      if (!o || o.status !== "placed") return false;
      o.status = "cancelled"; o.history.push({ status: "cancelled", at: new Date().toISOString() });
      write(ORDERS_KEY, orders);
      return true;
    },
    reorder: function (order) {
      var added = 0;
      order.items.forEach(function (i) {
        if (Store.addToCart(i.product_id, { size: i.size, color: i.color, qty: i.quantity })) added++;
      });
      return added;
    }
  };

  function readText(key) { try { return localStorage.getItem(key) || ""; } catch (e) { return ""; } }
  function writeText(key, val) { try { localStorage.setItem(key, val); } catch (e) { /* storage unavailable */ } }

  function randomHex(bytes) {
    var a = new Uint8Array(bytes);
    window.crypto.getRandomValues(a);
    return Array.prototype.map.call(a, function (b) { return ("0" + b.toString(16)).slice(-2); }).join("");
  }

  /* ---------- password hashing ---------- */
  function fromHex(hex) { return new Uint8Array(hex.match(/../g).map(function (h) { return parseInt(h, 16); })); }
  function derive(password, saltHex) {
    var subtle = window.crypto && window.crypto.subtle;
    if (!subtle) return Promise.reject(new Error("insecure"));
    return subtle.importKey("raw", new TextEncoder().encode(password), "PBKDF2", false, ["deriveBits"]).then(function (key) {
      return subtle.deriveBits({ name: "PBKDF2", salt: fromHex(saltHex), iterations: 150000, hash: "SHA-256" }, key, 256);
    }).then(function (bits) {
      return Array.prototype.map.call(new Uint8Array(bits), function (b) { return ("0" + b.toString(16)).slice(-2); }).join("");
    });
  }
  var INSECURE = { ok: false, message: "Accounts need a secure (https) connection. Please reload the site over https." };

  /* ---------- customer accounts (demo, see the note at the top of this file) ---------- */
  function sessionId() {
    try { return sessionStorage.getItem(SESSION_KEY) || localStorage.getItem(SESSION_KEY); } catch (e) { return null; }
  }
  function startSession(id, remember) {
    try {
      sessionStorage.removeItem(SESSION_KEY); localStorage.removeItem(SESSION_KEY);
      (remember ? localStorage : sessionStorage).setItem(SESSION_KEY, id);
    } catch (e) { /* storage unavailable */ }
    Store.emit("ashfi:auth");
  }
  function findByEmail(email) {
    email = String(email || "").trim().toLowerCase();
    return read(USERS_KEY).filter(function (u) { return u.email === email; })[0] || null;
  }
  function updateUser(id, fn) {
    var users = read(USERS_KEY), user = users.filter(function (u) { return u.id === id; })[0];
    if (!user) return null;
    fn(user);
    write(USERS_KEY, users);
    Store.emit("ashfi:auth");
    return user;
  }

  var Auth = {
    /** The signed-in customer without their password fields, or null. */
    current: function () {
      var id = sessionId(), user = id && read(USERS_KEY).filter(function (u) { return u.id === id; })[0];
      if (!user) return null;
      var copy = Object.assign({}, user);
      delete copy.hash; delete copy.salt;
      return copy;
    },
    register: function (d) {
      if (findByEmail(d.email)) return Promise.resolve({ ok: false, field: "email", message: "An account with this email already exists. Try logging in." });
      var salt = randomHex(16);
      return derive(d.password, salt).then(function (hash) {
        var user = { id: "u_" + randomHex(6), name: d.name.trim(), email: d.email.trim().toLowerCase(), phone: Store.normalizePhone(d.phone), salt: salt, hash: hash, addresses: [], createdAt: new Date().toISOString() };
        var users = read(USERS_KEY); users.push(user); write(USERS_KEY, users);
        startSession(user.id, false);
        return { ok: true };
      }, function () { return INSECURE; });
    },
    login: function (email, password, remember) {
      var bad = { ok: false, message: "Email or password is incorrect." }, user = findByEmail(email);
      if (!user) return derive(password, "00").then(function () { return bad; }, function () { return INSECURE; });
      return derive(password, user.salt).then(function (hash) {
        if (hash !== user.hash) return bad;
        startSession(user.id, remember);
        return { ok: true };
      }, function () { return INSECURE; });
    },
    logout: function () {
      try { sessionStorage.removeItem(SESSION_KEY); localStorage.removeItem(SESSION_KEY); } catch (e) { /* storage unavailable */ }
      Store.emit("ashfi:auth");
    },
    updateProfile: function (d) {
      var me = Auth.current(), other = findByEmail(d.email);
      if (other && other.id !== me.id) return { ok: false, field: "email", message: "Another account already uses this email." };
      updateUser(me.id, function (u) { u.name = d.name.trim(); u.email = d.email.trim().toLowerCase(); u.phone = Store.normalizePhone(d.phone); });
      return { ok: true };
    },
    changePassword: function (current, next) {
      var me = Auth.current(), stored = me && findByEmail(me.email);
      if (!stored) return Promise.resolve({ ok: false, message: "Please log in again." });
      return derive(current, stored.salt).then(function (hash) {
        if (hash !== stored.hash) return { ok: false, field: "pwCurrent", message: "Your current password is incorrect." };
        var salt = randomHex(16);
        return derive(next, salt).then(function (h) {
          updateUser(me.id, function (u) { u.salt = salt; u.hash = h; });
          return { ok: true };
        });
      }, function () { return INSECURE; });
    },
    /* Demo only: a real reset must email a single-use link from the server. Here the
       token just stays in this tab, so returns null when no account matches. */
    requestReset: function (email) {
      var user = findByEmail(email);
      if (!user) return null;
      var token = randomHex(16);
      try { sessionStorage.setItem(RESET_KEY, JSON.stringify({ token: token, email: user.email, expires: Date.now() + 15 * 60 * 1000 })); } catch (e) { return null; }
      return token;
    },
    resetPassword: function (token, password) {
      var r = null;
      try { r = JSON.parse(sessionStorage.getItem(RESET_KEY)); } catch (e) { /* unreadable token */ }
      var user = r && r.token === token && r.expires > Date.now() ? findByEmail(r.email) : null;
      if (!user) return Promise.resolve({ ok: false, message: "This reset link is invalid or has expired." });
      var salt = randomHex(16);
      return derive(password, salt).then(function (h) {
        updateUser(user.id, function (u) { u.salt = salt; u.hash = h; });
        try { sessionStorage.removeItem(RESET_KEY); } catch (e) { /* storage unavailable */ }
        return { ok: true };
      }, function () { return INSECURE; });
    },

    /* ---------- address book ---------- */
    defaultAddress: function () {
      var me = Auth.current();
      if (!me || !me.addresses.length) return null;
      return me.addresses.filter(function (a) { return a.isDefault; })[0] || me.addresses[0];
    },
    saveAddress: function (a) {
      var me = Auth.current();
      updateUser(me.id, function (u) {
        var existing = u.addresses.filter(function (x) { return x.id === a.id; })[0];
        var makeDefault = a.isDefault || !u.addresses.length;
        if (makeDefault) u.addresses.forEach(function (x) { x.isDefault = false; });
        if (existing) Object.assign(existing, a, { isDefault: makeDefault || existing.isDefault });
        else u.addresses.push(Object.assign({}, a, { id: "a_" + randomHex(4), isDefault: makeDefault }));
      });
    },
    deleteAddress: function (id) {
      var me = Auth.current();
      updateUser(me.id, function (u) {
        u.addresses = u.addresses.filter(function (x) { return x.id !== id; });
        if (u.addresses.length && !u.addresses.some(function (x) { return x.isDefault; })) u.addresses[0].isDefault = true;
      });
    },
    setDefaultAddress: function (id) {
      var me = Auth.current();
      updateUser(me.id, function (u) { u.addresses.forEach(function (x) { x.isDefault = x.id === id; }); });
    }
  };

  window.Store = Store;
  window.Auth = Auth;
})();
