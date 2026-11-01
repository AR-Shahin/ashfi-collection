/**
 * Ashfi Collection – shared state & helpers (cart, wishlist, formatting).
 * Cart/wishlist live in localStorage. The storage layer is isolated here so it
 * can later be swapped for API calls (e.g. Laravel sessions) in one place.
 */
(function () {
  "use strict";
  var D = window.ASHFI;
  var CART_KEY = "ashfi_cart_v1";
  var WISH_KEY = "ashfi_wishlist_v1";

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
    }
  };

  window.Store = Store;
})();
