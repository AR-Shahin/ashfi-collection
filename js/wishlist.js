/* Wishlist page – saved on this device via localStorage. */
(function () {
  "use strict";
  var S = window.Store, U = window.UI;

  function render() {
    var root = document.getElementById("wishRoot");
    var items = S.getWishlist().map(S.getProduct).filter(Boolean);
    if (!items.length) {
      root.innerHTML = '<div class="empty">' + U.icon("heart", 48) + "<h2>Your wishlist is empty</h2>" +
        "<p>Tap the heart on any product to save it here for later.</p>" +
        '<a class="btn btn--primary" href="shop.html">Discover Products</a></div>';
      return;
    }
    root.innerHTML = '<p class="shop__count" style="margin-bottom:20px">' + items.length + " saved item" + (items.length === 1 ? "" : "s") + '</p><div class="grid" id="wishGrid"></div>';
    U.renderProducts(document.getElementById("wishGrid"), items);
  }

  document.addEventListener("ashfi:ready", function () {
    render();
    document.addEventListener("ashfi:wishlist", render);
  });
})();
