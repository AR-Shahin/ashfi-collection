/* Homepage slider. Slides already in the page are kept; the rest come from
 * ASHFI.heroSlides. Everything after that reads the slide's data-* attributes, so
 * server-rendered slides (Blade) behave the same way.
 */
(function () {
  "use strict";
  var root = document.getElementById("heroSlider");
  if (!root) return;
  var S = window.Store, U = window.UI, esc = S.escape;
  var track = document.getElementById("heroTrack");
  var reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var SWAP = { curtain: 630, iris: 420, diagonal: 420 }, END = { curtain: 1260, iris: 950, diagonal: 950 };
  var ICONS = {
    pause: '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M4 2h3v12H4zM9 2h3v12H9z"/></svg>',
    play: '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M4 2l10 6-10 6z"/></svg>'
  };

  function picture(s) {
    var i = s.image;
    return "<picture>" +
      '<source media="(max-width: 575px)" srcset="' + esc(i.sm) + '" width="750" height="1100">' +
      '<source media="(max-width: 991px)" srcset="' + esc(i.md) + '" width="1200" height="900">' +
      '<img src="' + esc(i.lg) + '" alt="' + esc(i.alt) + '" width="1920" height="1000" loading="lazy" decoding="async"></picture>';
  }

  function slideHtml(s) {
    var video = s.video ? ' data-video="' + esc(s.video.lg) + '" data-video-sm="' + esc(s.video.sm) + '"' : "";
    return '<article class="hero-slide" data-align="' + esc(s.align || "left") + '" data-fx="' + esc(s.transition || "curtain") + '" data-duration="' + (+s.duration || 5000) + '"' + video + ">" +
      '<div class="hero-slide__media">' + picture(s) + "</div>" +
      '<div class="container hero-slide__inner"><div class="hero-slide__copy">' +
        '<p class="hero-slide__motto"' + (s.mottoLang ? ' lang="' + esc(s.mottoLang) + '"' : "") + ">" + esc(s.motto) + "</p>" +
        '<h2 class="hero-slide__title">' + esc(s.title) + "</h2>" +
        '<p class="hero-slide__desc">' + esc(s.description) + "</p>" +
        '<div class="hero-slide__cta"><a class="btn btn--light" href="' + esc(s.primary.href) + '">' + esc(s.primary.label) + "</a>" +
          '<a class="btn btn--ghost-light" href="' + esc(s.secondary.href) + '">' + esc(s.secondary.label) + "</a></div>" +
      "</div></div></article>";
  }

  (window.ASHFI.heroSlides || []).slice(track.children.length).forEach(function (s) {
    track.insertAdjacentHTML("beforeend", slideHtml(s));
  });

  var slides = [].slice.call(track.children), current = 0, busy = false, started = false, tabs = [], pausedBy = {};
  if (!slides.length) return;

  function title(slide) { return slide.querySelector(".hero-slide__title").textContent.trim(); }

  // each word rises out of its own mask
  function splitTitle(slide) {
    var el = slide.querySelector(".hero-slide__title");
    el.innerHTML = el.textContent.trim().split(/\s+/).map(function (w, i) {
      return '<span class="w"><span style="--i:' + i + '">' + esc(w) + "</span></span>";
    }).join(" ");
  }

  slides.forEach(function (slide, i) {
    splitTitle(slide);
    slide.setAttribute("role", "group");
    slide.setAttribute("aria-roledescription", "slide");
    slide.setAttribute("aria-label", (i + 1) + " of " + slides.length);
    if (i) { slide.inert = true; slide.setAttribute("aria-hidden", "true"); }
  });
  root.classList.add("is-js");
  root.insertAdjacentHTML("beforeend", '<span class="hero__ring" aria-hidden="true"></span>');
  if (slides.length < 2) { startVideo(slides[0]); return; }

  var curtain = document.createElement("div");
  curtain.className = "hero__curtain"; curtain.setAttribute("aria-hidden", "true");
  curtain.innerHTML = [0, 1, 2, 3, 4, 5, 6].map(function (i) { return '<i style="--i:' + i + '"></i>'; }).join("");
  root.appendChild(curtain);

  var controls = document.createElement("div");
  controls.className = "hero__controls";
  controls.innerHTML = '<div class="container hero__controls-row"><ul class="hero-tabs">' + slides.map(function (slide, i) {
    return '<li><button type="button" class="hero-tab" data-go="' + i + '" aria-label="Go to slide ' + (i + 1) + ": " + esc(title(slide)) + '">' +
      '<span class="hero-tab__bar"><i></i></span><span class="hero-tab__label" aria-hidden="true"><b>0' + (i + 1) + "</b>" + esc(title(slide)) + "</span></button></li>";
  }).join("") + "</ul>" +
    '<button type="button" class="hero__btn" id="heroToggle"></button>' +
    '<button type="button" class="hero__btn hero__btn--arrow" data-step="-1" aria-label="Previous slide">' + U.icon("left", 18) + "</button>" +
    '<button type="button" class="hero__btn hero__btn--arrow" data-step="1" aria-label="Next slide">' + U.icon("right", 18) + "</button></div>";
  root.appendChild(controls);
  tabs = [].slice.call(controls.querySelectorAll(".hero-tab"));
  var toggle = document.getElementById("heroToggle");

  function canPlayVideo() {
    var c = navigator.connection;
    return !reduced && !(c && (c.saveData || /2g/.test(c.effectiveType || "")));
  }

  function startVideo(slide) {
    if (!slide.dataset.video || !canPlayVideo()) return;
    var v = slide.querySelector("video");
    if (!v) {
      v = document.createElement("video");
      v.muted = true; v.loop = true; v.playsInline = true; v.preload = "auto";
      v.setAttribute("aria-hidden", "true"); v.tabIndex = -1;
      v.src = window.matchMedia("(max-width: 767px)").matches ? slide.dataset.videoSm : slide.dataset.video;
      v.addEventListener("playing", function () { slide.classList.add("has-video"); });
      slide.querySelector(".hero-slide__media").appendChild(v);
    }
    var p = v.play();
    if (p && p.catch) p.catch(function () {}); // blocked or missing: the image stays
  }

  function stopVideo(slide) { var v = slide.querySelector("video"); if (v) v.pause(); }

  function setAuto() {
    var off = reduced || pausedBy.user;
    root.classList.toggle("is-auto", !reduced);
    toggle.hidden = reduced;
    track.setAttribute("aria-live", off ? "polite" : "off");
    toggle.innerHTML = off ? ICONS.play : ICONS.pause;
    toggle.setAttribute("aria-label", off ? "Play slideshow" : "Pause slideshow");
  }

  function videoHeld() { return pausedBy.user || pausedBy.offscreen || pausedBy.hidden; }

  function pause(reason, on) {
    if (on) pausedBy[reason] = true; else delete pausedBy[reason];
    root.classList.toggle("is-paused", Object.keys(pausedBy).length > 0);
    setAuto();
    if (!started) return;
    if (videoHeld()) stopVideo(slides[current]); else startVideo(slides[current]);
  }

  function markTabs() {
    // the next transition takes this long to show its slide, so it comes out of the wait
    var lead = SWAP[slides[(current + 1) % slides.length].dataset.fx] || 0;
    root.style.setProperty("--dur", ((+slides[current].dataset.duration || 5000) - lead) + "ms");
    tabs.forEach(function (t, i) {
      t.classList.toggle("is-active", i === current);
      t.classList.toggle("is-done", i < current);
      if (i === current) t.setAttribute("aria-current", "true"); else t.removeAttribute("aria-current");
    });
  }

  // keeps the following slide laid out so its lazy image loads ahead of time
  function warmNext() {
    slides.forEach(function (s) { s.classList.remove("is-next"); });
    var n = slides[(current + 1) % slides.length];
    if (n !== slides[current]) n.classList.add("is-next");
  }

  function whenReady(slide, done) {
    var img = slide.querySelector("img"), called = false;
    var go = function () { if (!called) { called = true; done(); } };
    slide.classList.add("is-next");
    if (!img || (img.complete && img.naturalWidth)) { go(); return; }
    img.loading = "eager";
    img.addEventListener("load", go); img.addEventListener("error", go);
    setTimeout(go, 900);
  }

  function setAccess(slide, on) {
    slide.inert = !on;
    if (on) slide.removeAttribute("aria-hidden"); else slide.setAttribute("aria-hidden", "true");
  }

  function finish(prev, next) {
    prev.classList.remove("is-active", "has-video");
    next.classList.remove("is-entering");
    busy = false;
    warmNext();
  }

  function goTo(i) {
    i = (i + slides.length) % slides.length;
    if (busy || i === current) return;
    busy = true;
    var prev = slides[current], next = slides[i], fx = next.dataset.fx;
    whenReady(next, function () {
      next.classList.remove("is-next");
      var swap = function () {
        prev.classList.remove("is-live"); setAccess(prev, false); stopVideo(prev);
        next.classList.add("is-active", "is-live"); setAccess(next, true);
        current = i; markTabs();
        if (!videoHeld()) startVideo(next);
      };
      if (reduced) { swap(); finish(prev, next); return; }
      if (fx === "iris" || fx === "diagonal") {
        next.classList.add("is-active", "is-entering");
        prev.classList.remove("is-live");
        setTimeout(swap, SWAP[fx]);
        setTimeout(function () { finish(prev, next); }, END[fx]);
        return;
      }
      curtain.className = "hero__curtain is-in";
      setTimeout(function () { swap(); prev.classList.remove("is-active"); curtain.className = "hero__curtain is-out"; }, SWAP.curtain);
      setTimeout(function () { curtain.className = "hero__curtain"; finish(prev, next); }, END.curtain);
    });
  }

  controls.addEventListener("click", function (e) {
    var tab = e.target.closest("[data-go]"), step = e.target.closest("[data-step]");
    if (tab) goTo(+tab.getAttribute("data-go"));
    if (step) goTo(current + (+step.getAttribute("data-step")));
    if (e.target.closest("#heroToggle")) pause("user", !pausedBy.user);
  });
  // the gold bar finishing is the autoplay clock, so pausing it also pauses the slideshow
  controls.addEventListener("animationend", function (e) {
    if (e.animationName === "heroFill") goTo(current + 1);
  });

  root.addEventListener("keydown", function (e) {
    if (e.key === "ArrowLeft") goTo(current - 1);
    if (e.key === "ArrowRight") goTo(current + 1);
  });
  if (window.matchMedia("(hover: hover)").matches) {
    root.addEventListener("mouseenter", function () { pause("hover", true); });
    root.addEventListener("mouseleave", function () { pause("hover", false); });
  }
  root.addEventListener("focusin", function (e) { if (e.target.matches(":focus-visible")) pause("focus", true); });
  root.addEventListener("focusout", function (e) { if (!root.contains(e.relatedTarget)) pause("focus", false); });
  document.addEventListener("visibilitychange", function () { pause("hidden", document.hidden); });
  if ("IntersectionObserver" in window) {
    new IntersectionObserver(function (entries) { pause("offscreen", !entries[0].isIntersecting); }, { threshold: .2 }).observe(root);
  }

  // swipe
  var startX = null, startY = 0;
  root.addEventListener("pointerdown", function (e) {
    if (e.pointerType !== "mouse") { startX = e.clientX; startY = e.clientY; }
  });
  root.addEventListener("pointerup", function (e) {
    if (startX === null) return;
    var dx = e.clientX - startX, dy = e.clientY - startY;
    startX = null;
    if (Math.abs(dx) > 48 && Math.abs(dx) > Math.abs(dy) * 1.4) goTo(current + (dx < 0 ? 1 : -1));
  });
  root.addEventListener("pointercancel", function () { startX = null; });

  // gentle depth on mouse movement
  if (!reduced && window.matchMedia("(hover: hover) and (pointer: fine)").matches) {
    var frame = 0;
    root.addEventListener("pointermove", function (e) {
      if (frame) return;
      frame = requestAnimationFrame(function () {
        frame = 0;
        var r = root.getBoundingClientRect();
        root.style.setProperty("--px", (((e.clientX - r.left) / r.width - .5) * 2).toFixed(3));
        root.style.setProperty("--py", (((e.clientY - r.top) / r.height - .5) * 2).toFixed(3));
      });
    });
  }

  markTabs();
  setAuto();
  var begin = function () { started = true; if (!videoHeld()) startVideo(slides[0]); warmNext(); };
  if (document.readyState === "complete") begin(); else window.addEventListener("load", begin);
})();
