/* Login, register, forgot password and reset password pages. */
(function () {
  "use strict";
  var S = window.Store, U = window.UI, A = window.Auth, esc = S.escape;
  var $ = function (id) { return document.getElementById(id); };
  var params = new URLSearchParams(location.search);

  /* Only ever follow ?next= to a page on this site. */
  function nextPage() {
    var next = params.get("next") || "";
    return /^[a-z0-9-]+\.html(\?[\w=&%.-]*)?$/.test(next) ? next : "account.html";
  }

  function showMsg(text) {
    var m = $("formMsg"); if (!m) return;
    m.textContent = text || ""; m.hidden = !text;
  }

  /** Runs each [field, message-or-empty] check, focusing the first problem. */
  function validate(checks) {
    var first = null;
    checks.forEach(function (c) { if (!U.fieldError(c[0], c[1]) && !first) first = c[0]; });
    if (first) first.focus();
    return !first;
  }

  function clearOnInput(form) {
    form.addEventListener("input", function (e) { if (e.target.getAttribute("aria-invalid") === "true") U.fieldError(e.target, ""); });
  }

  function busy(form, on, label) {
    var b = form.querySelector('[type="submit"]');
    if (on) { b.setAttribute("data-label", b.textContent); b.textContent = label; } else b.textContent = b.getAttribute("data-label");
    b.disabled = on;
  }

  function passwordChecks(pw, confirm) {
    return [[pw, pw.value.length < 8 ? "Use at least 8 characters." : ""], [confirm, confirm.value !== pw.value ? "Passwords don’t match." : ""]];
  }

  function initLogin(form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault(); showMsg("");
      var email = $("loginEmail"), pw = $("loginPassword");
      if (!validate([[email, S.isEmail(email.value) ? "" : "Enter a valid email address."], [pw, pw.value ? "" : "Enter your password."]])) return;
      busy(form, true, "Logging in…");
      A.login(email.value, pw.value, $("remember").checked).then(function (res) {
        if (res.ok) { location.href = nextPage(); return; }
        busy(form, false); showMsg(res.message);
      });
    });
  }

  function initRegister(form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault(); showMsg("");
      var name = $("regName"), email = $("regEmail"), phone = $("regPhone"), pw = $("regPassword"), confirm = $("regConfirm"), terms = $("regTerms");
      if (!validate([
        [name, name.value.trim().length < 2 ? "Please enter your full name." : ""],
        [email, S.isEmail(email.value) ? "" : "Enter a valid email address."],
        [phone, S.isPhone(phone.value) ? "" : "Enter a valid Bangladeshi mobile number (e.g. 01712345678)."]
      ].concat(passwordChecks(pw, confirm), [[terms, terms.checked ? "" : "Please accept the terms to continue."]]))) return;
      busy(form, true, "Creating account…");
      A.register({ name: name.value, email: email.value, phone: phone.value, password: pw.value }).then(function (res) {
        if (res.ok) { location.href = nextPage(); return; }
        busy(form, false);
        if (res.field === "email") { U.fieldError(email, res.message); email.focus(); } else showMsg(res.message);
      });
    });
  }

  function initForgot(form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var email = $("forgotEmail");
      if (!validate([[email, S.isEmail(email.value) ? "" : "Enter a valid email address."]])) return;
      var token = A.requestReset(email.value), done = $("forgotDone");
      // A live site must always show the same message and send the link by email.
      done.innerHTML = '<h2>Check your details</h2>' + (token
        ? '<p class="notice">' + U.icon("info", 18) + "<span>Demo store: no email is sent, so continue with the link below. It works in this tab for 15 minutes.</span></p>" +
          '<a class="btn btn--primary btn--block" href="reset-password.html?token=' + esc(token) + '">Choose a New Password</a>'
        : "<p>We couldn’t find an account for that email in this browser. Check the spelling, or create a new account.</p>" +
          '<a class="btn btn--outline btn--block" href="register.html">Create Account</a>');
      $("forgotStep").hidden = true; done.hidden = false; done.focus();
    });
  }

  function initReset(form) {
    var token = params.get("token");
    if (!token) showMsg("This reset link is invalid or has expired.");
    form.addEventListener("submit", function (e) {
      e.preventDefault(); showMsg("");
      var pw = $("resetPassword"), confirm = $("resetConfirm");
      if (!validate(passwordChecks(pw, confirm))) return;
      busy(form, true, "Saving…");
      A.resetPassword(token, pw.value).then(function (res) {
        if (!res.ok) { busy(form, false); showMsg(res.message); return; }
        U.toast("Password updated. Please log in.");
        setTimeout(function () { location.href = "login.html"; }, 900);
      });
    });
  }

  document.addEventListener("ashfi:ready", function () {
    var login = $("loginForm"), reg = $("registerForm"), forgot = $("forgotForm"), reset = $("resetForm");
    if ((login || reg) && A.current()) { location.replace(nextPage()); return; }
    var link = $("switchLink");
    if (link && params.get("next")) link.href += "?next=" + encodeURIComponent(nextPage());
    [login, reg, forgot, reset].forEach(function (f) { if (f) clearOnInput(f); });
    if (login) initLogin(login);
    if (reg) initRegister(reg);
    if (forgot) initForgot(forgot);
    if (reset) initReset(reset);
  });
})();
