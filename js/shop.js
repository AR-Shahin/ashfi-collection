/* Shop page: filtering, sorting, searching – state is mirrored in the URL. */
(function () {
  "use strict";
  var S = window.Store, D = S.data, U = window.UI, esc = S.escape;
  var $ = function (id) { return document.getElementById(id); };

  var PRICE_PRESETS = [
    { label: "Under ৳2,000", min: "", max: 2000 },
    { label: "৳2,000 – ৳4,000", min: 2000, max: 4000 },
    { label: "৳4,000 – ৳6,000", min: 4000, max: 6000 },
    { label: "Over ৳6,000", min: 6000, max: "" }
  ];

  var state = { category: "", q: "", min: "", max: "", sale: false, isNew: false, best: false, sort: "latest" };

  function readUrl() {
    var q = new URLSearchParams(location.search);
    var cat = q.get("category");
    state.category = D.categories.some(function (c) { return c.slug === cat && c.slug !== "new"; }) ? cat : "";
    state.q = (q.get("q") || "").trim();
    state.min = q.get("min") || ""; state.max = q.get("max") || "";
    var f = q.get("filter");
    state.isNew = f === "new" || q.get("new") === "1";
    state.best = f === "best" || q.get("best") === "1";
    state.sale = f === "sale" || q.get("sale") === "1";
    var s = q.get("sort");
    state.sort = ["latest", "price-asc", "price-desc", "discount"].indexOf(s) > -1 ? s : "latest";
  }

  function writeUrl() {
    var q = new URLSearchParams();
    if (state.category) q.set("category", state.category);
    if (state.q) q.set("q", state.q);
    if (state.min !== "") q.set("min", state.min);
    if (state.max !== "") q.set("max", state.max);
    if (state.isNew) q.set("new", "1");
    if (state.best) q.set("best", "1");
    if (state.sale) q.set("sale", "1");
    if (state.sort !== "latest") q.set("sort", state.sort);
    var qs = q.toString();
    try { history.replaceState(null, "", location.pathname + (qs ? "?" + qs : "")); } catch (e) { /* file:// */ }
  }

  function filtered() {
    var min = state.min === "" ? 0 : Number(state.min), max = state.max === "" ? Infinity : Number(state.max);
    var list = D.products.filter(function (p) {
      var price = S.unitPrice(p);
      if (state.category && p.category !== state.category) return false;
      if (price < min || price > max) return false;
      if (state.sale && !S.discountPct(p)) return false;
      if (state.isNew && !p.isNew) return false;
      if (state.best && !p.isBestSeller) return false;
      return true;
    });
    if (state.q) {
      var hits = window.searchProducts(state.q).map(function (p) { return p.id; });
      list = list.filter(function (p) { return hits.indexOf(p.id) > -1; });
    }
    var sorters = {
      "latest": function (a, b) { return b.createdAt.localeCompare(a.createdAt); },
      "price-asc": function (a, b) { return S.unitPrice(a) - S.unitPrice(b); },
      "price-desc": function (a, b) { return S.unitPrice(b) - S.unitPrice(a); },
      "discount": function (a, b) { return S.discountPct(b) - S.discountPct(a); }
    };
    return list.sort(sorters[state.sort]);
  }

  function title() {
    var cat = state.category && S.getCategory(state.category);
    if (state.q) return { h: "Results for “" + state.q + "”", sub: "Showing products that match your search." };
    if (state.isNew && !state.category) return { h: "New Arrivals", sub: "The newest additions to the Ashfi Collection." };
    if (state.best && !state.category) return { h: "Best Sellers", sub: "The pieces our customers love most." };
    if (state.sale && !state.category) return { h: "Sale & Offers", sub: "Elegant pieces at special prices." };
    if (cat) return { h: cat.name, sub: cat.blurb + " – explore the " + cat.name.toLowerCase() + " collection." };
    return { h: "All Products", sub: "Discover elegant outfits for every occasion." };
  }

  function renderFilters() {
    var counts = {};
    D.products.forEach(function (p) { counts[p.category] = (counts[p.category] || 0) + 1; });
    var html = '<label class="f-opt"><input type="radio" name="fcat" value=""' + (!state.category ? " checked" : "") + '> All Products<small>' + D.products.length + "</small></label>";
    D.categories.filter(function (c) { return c.slug !== "new"; }).forEach(function (c) {
      html += '<label class="f-opt"><input type="radio" name="fcat" value="' + c.slug + '"' + (state.category === c.slug ? " checked" : "") + "> " + esc(c.name) + "<small>" + (counts[c.slug] || 0) + "</small></label>";
    });
    $("fCats").innerHTML = html;
    $("pChips").innerHTML = PRICE_PRESETS.map(function (p, i) {
      var on = String(state.min) === String(p.min) && String(state.max) === String(p.max);
      return '<button type="button" class="chip-btn" data-preset="' + i + '" aria-pressed="' + on + '">' + p.label + "</button>";
    }).join("");
    $("pMin").value = state.min; $("pMax").value = state.max;
    $("fSale").checked = state.sale; $("fNew").checked = state.isNew; $("fBest").checked = state.best;
    $("sortSel").value = state.sort;
    $("shopQ").value = state.q; $("shopQClear").hidden = !state.q;
  }

  function renderTags() {
    var tags = [];
    var add = function (label, key) { tags.push('<span class="tag">' + esc(label) + '<button type="button" data-clear="' + key + '" aria-label="Remove filter: ' + esc(label) + '">' + U.icon("x", 12) + "</button></span>"); };
    if (state.category) add(S.getCategory(state.category).name, "category");
    if (state.q) add("Search: " + state.q, "q");
    if (state.min !== "" || state.max !== "") add("৳" + (state.min || 0) + " – " + (state.max === "" ? "any" : "৳" + state.max), "price");
    if (state.sale) add("On sale", "sale");
    if (state.isNew) add("New arrivals", "isNew");
    if (state.best) add("Best sellers", "best");
    if (tags.length > 1) tags.push('<button type="button" class="chip-btn" data-clear="all">Clear all</button>');
    $("activeFilters").innerHTML = tags.join("");
  }

  function render() {
    var list = filtered(), t = title();
    $("shopTitle").textContent = t.h;
    var sub = $("shopTitleSub"); if (sub) sub.textContent = t.sub;
    document.title = (t.h === "All Products" ? "Shop All" : t.h) + " | Ashfi Collection";
    $("shopCount").innerHTML = "Showing <strong>" + list.length + "</strong> of " + D.products.length + " products";
    $("fApply").textContent = "Show " + list.length + " product" + (list.length === 1 ? "" : "s");
    var grid = $("shopGrid");
    if (!list.length) {
      grid.className = "";
      grid.innerHTML = '<div class="empty">' + U.icon("search", 44) + "<h2>No products found</h2>" +
        "<p>We couldn’t find anything matching your filters. Try a different search, widen your price range, or clear the filters.</p>" +
        '<button type="button" class="btn btn--primary" data-clear="all">Clear all filters</button></div>';
    } else {
      grid.className = "grid";
      U.renderProducts(grid, list);
    }
    renderTags();
    writeUrl();
  }

  function clear(key) {
    if (key === "all") { state.category = ""; state.q = ""; state.min = ""; state.max = ""; state.sale = state.isNew = state.best = false; }
    if (key === "category") state.category = "";
    if (key === "q") state.q = "";
    if (key === "price") { state.min = ""; state.max = ""; }
    if (key === "sale") state.sale = false;
    if (key === "isNew") state.isNew = false;
    if (key === "best") state.best = false;
    renderFilters(); render();
  }

  function setDrawer(open) {
    $("filters").classList.toggle("is-open", open);
    $("filterOverlay").hidden = !open;
    document.documentElement.classList.toggle("no-scroll", open);
    $("filterOpen").setAttribute("aria-expanded", open ? "true" : "false");
    if (open) $("filterClose").focus(); else $("filterOpen").focus();
  }

  function init() {
    readUrl(); renderFilters(); render();
    $("filterOpen").setAttribute("aria-controls", "filters");
    $("filterOpen").setAttribute("aria-expanded", "false");

    $("fCats").addEventListener("change", function (e) { state.category = e.target.value; render(); });
    $("sortSel").addEventListener("change", function (e) { state.sort = e.target.value; render(); });
    ["fSale", "fNew", "fBest"].forEach(function (id) {
      $(id).addEventListener("change", function () { state.sale = $("fSale").checked; state.isNew = $("fNew").checked; state.best = $("fBest").checked; render(); });
    });
    var t;
    var priceInput = function () {
      clearTimeout(t);
      t = setTimeout(function () {
        state.min = $("pMin").value; state.max = $("pMax").value;
        $("pChips").querySelectorAll(".chip-btn").forEach(function (b) {
          var p = PRICE_PRESETS[b.getAttribute("data-preset")];
          b.setAttribute("aria-pressed", String(String(state.min) === String(p.min) && String(state.max) === String(p.max)));
        });
        render();
      }, 250);
    };
    $("pMin").addEventListener("input", priceInput); $("pMax").addEventListener("input", priceInput);
    $("pChips").addEventListener("click", function (e) {
      var b = e.target.closest("[data-preset]"); if (!b) return;
      var p = PRICE_PRESETS[b.getAttribute("data-preset")];
      var same = String(state.min) === String(p.min) && String(state.max) === String(p.max);
      state.min = same ? "" : p.min; state.max = same ? "" : p.max;
      renderFilters(); render();
    });
    $("shopSearch").addEventListener("submit", function (e) { e.preventDefault(); });
    var st;
    $("shopQ").addEventListener("input", function (e) {
      clearTimeout(st);
      st = setTimeout(function () { state.q = e.target.value.trim(); $("shopQClear").hidden = !state.q; render(); }, 200);
    });
    $("shopQClear").addEventListener("click", function () { $("shopQ").value = ""; clear("q"); $("shopQ").focus(); });
    document.addEventListener("click", function (e) {
      var c = e.target.closest("[data-clear]"); if (c) clear(c.getAttribute("data-clear"));
    });
    $("filterOpen").addEventListener("click", function () { setDrawer(true); });
    ["filterClose", "fApply"].forEach(function (id) { $(id).addEventListener("click", function () { setDrawer(false); }); });
    $("filterOverlay").addEventListener("click", function () { setDrawer(false); });
    $("fClearM").addEventListener("click", function () { clear("all"); });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape" && $("filters").classList.contains("is-open")) setDrawer(false); });
  }

  /* Filtered views share one canonical URL each; searches stay out of the index. */
  function shopSeo() {
    var q = new URLSearchParams(location.search), cat = S.getCategory(q.get("category"));
    var path = "shop.html" + (cat ? "?category=" + cat.slug : q.get("filter") ? "?filter=" + encodeURIComponent(q.get("filter")) : "");
    U.setCanonical(path);
    if (cat) {
      var desc = "Shop " + cat.name + " at Ashfi Collection: " + cat.blurb.toLowerCase() + " with nationwide delivery and Cash on Delivery in Bangladesh.";
      ['meta[name="description"]', 'meta[property="og:description"]'].forEach(function (sel) { document.querySelector(sel).setAttribute("content", desc); });
    }
    if (q.get("q")) document.querySelector('meta[name="robots"]').setAttribute("content", "noindex,follow");
  }
  document.addEventListener("ashfi:ready", shopSeo);
  document.addEventListener("ashfi:ready", init);
})();
