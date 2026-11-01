/* Policy page: fills delivery charges and photo credits from data.js and icons. */
document.addEventListener("ashfi:ready", function () {
  var S = window.Store, d = S.data.delivery;
  document.getElementById("polIn").textContent = S.money(d.insideDhaka);
  document.getElementById("polOut").textContent = S.money(d.outsideDhaka);
  document.getElementById("polFree").textContent = "Orders over " + S.money(d.freeOver) + " qualify for free delivery.";
  document.getElementById("polCredits").innerHTML = S.data.photoCredits.map(function (c) {
    return '<li><a href="' + c.url + '" target="_blank" rel="noopener">' + c.title + "</a> by " + c.author + ' (<a href="' + c.licenseUrl + '" target="_blank" rel="noopener">' + c.license + "</a>), used for: " + c.usedFor + ", cropped and resized.</li>";
  }).join("");
});
