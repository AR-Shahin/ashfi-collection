/* Policy page: fills delivery charges from data.js and icons. */
document.addEventListener("ashfi:ready", function () {
  var S = window.Store, d = S.data.delivery;
  document.getElementById("polIn").textContent = S.money(d.insideDhaka);
  document.getElementById("polOut").textContent = S.money(d.outsideDhaka);
  document.getElementById("polFree").textContent = "Orders over " + S.money(d.freeOver) + " qualify for free delivery.";
});
