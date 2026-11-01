/* Contact page: contact cards from data.js + demo form validation. */
(function () {
  "use strict";
  var S = window.Store, B = S.data.brand, U = window.UI, esc = S.escape;

  function card(ic, h, inner) {
    return '<div class="contact-card">' + U.icon(ic, 22) + "<div><h3>" + h + "</h3><p>" + inner + "</p></div></div>";
  }

  document.addEventListener("ashfi:ready", function () {
    var tel = B.phone.replace(/[^+\d]/g, "");
    document.getElementById("contactCards").innerHTML =
      card("phone", "Call us", '<a href="tel:' + tel + '">' + esc(B.phone) + "</a>") +
      card("chat", "WhatsApp", '<a href="https://wa.me/' + B.whatsapp + '" target="_blank" rel="noopener">Chat with us</a>') +
      card("mail", "Email", '<a href="mailto:' + B.email + '">' + esc(B.email) + "</a>") +
      card("pin", "Visit", esc(B.address)) +
      card("clock", "Opening hours", esc(B.hours)) +
      card("facebook", "Follow us", '<a href="' + B.facebook + '" target="_blank" rel="noopener">Facebook</a> · <a href="' + B.instagram + '" target="_blank" rel="noopener">Instagram</a>');

    var form = document.getElementById("contactForm"), msg = document.getElementById("contactMsg");
    function err(field, text) {
      field.setAttribute("aria-invalid", text ? "true" : "false");
      document.getElementById(field.id + "Err").textContent = text;
      return !text;
    }
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var f = form.elements, ok = true, first = null;
      var chk = function (field, text) { var g = err(field, text); if (!g && !first) first = field; ok = ok && g; };
      chk(f.name, f.name.value.trim().length < 2 ? "Please enter your name." : "");
      chk(f.phone, /^01[3-9]\d{8}$/.test(f.phone.value.replace(/[\s-]/g, "").replace(/^(\+?88)/, "")) ? "" : "Enter a valid mobile number (e.g. 01712345678).");
      chk(f.message, f.message.value.trim().length < 5 ? "Please write a short message." : "");
      if (first) { first.focus(); msg.textContent = ""; return; }
      msg.textContent = "Thank you! (Demo – your message was not actually sent.)";
      form.reset();
    });
  });
})();
