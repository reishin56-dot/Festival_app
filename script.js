function goToMenu() {
  const ticket = document.getElementById("ticketInput").value;

  if (ticket.trim() === "") {
    alert("Bitte Ticketnummer eingeben!");
    return;
  }

  window.location.href = "menu.html";
}

function goBack() {
  window.location.href = "index.html";
}

function goToPage(page) {
  window.location.href = page;
}


// Preise
let prices = {
  wasser: 5,
  cocktail: 12,
  bier: 8
};


// Menge ändern
function change(item, delta) {
  let el = document.getElementById(item);
  let value = parseInt(el.innerText);

  value += delta;

  // Minimum = 0
  if (value < 0) value = 0;

  el.innerText = value;
  updateTotal();
}


//NEU: Gesamt berechnen
function updateTotal() {
  let total = 0;

  for (let item in prices) {
    let amount = parseInt(document.getElementById(item).innerText);
    total += amount * prices[item];
  }

  document.getElementById("total").innerText = total;
}

const statusFlow = ["Erstellt", "In Zubereitung", "Bereit", "Ausgegeben"];

function toggleStatus(btn) {
  const nextIndex = (statusFlow.indexOf(btn.innerText) + 1) % statusFlow.length;
  btn.innerText = statusFlow[nextIndex];

  if (btn.innerText === "Ausgegeben") {
    btn.style.background = "#7dbb7f";
    btn.style.color = "#fff";
    btn.style.borderColor = "#5a9a5c";
  } else if (btn.innerText === "Bereit") {
    btn.style.background = "#8ad6ff";
    btn.style.borderColor = "#5aaed6";
    btn.style.color = "#0b2a3f";
  } else if (btn.innerText === "In Zubereitung") {
    btn.style.background = "#ffcc00";
    btn.style.borderColor = "#d4a400";
    btn.style.color = "#1a1a1a";
  } else {
    btn.style.background = "#dddddd";
    btn.style.borderColor = "#999999";
    btn.style.color = "#1a1a1a";
  }
}