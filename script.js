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
  bier: 8,
  credits5: 5,
  credits10: 9,
  credits20: 17,
  margherita: 5,
  spezial: 12,
  pepperoni: 8,
  ramen: 8,
  sushi: 10,
  dumplings: 6
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

// Gesamt berechnen
function updateTotal() {
  let total = 0;

  for (let item in prices) {
    let el = document.getElementById(item);
    if (el) {
      let amount = parseInt(el.innerText);
      total += amount * prices[item];
    }
  }

  let totalEl = document.getElementById("total");
  if (totalEl) {
    totalEl.innerText = total;
  }
}

const statusFlow = ["Erstellt", "In Zubereitung", "Bereit", "Ausgegeben"];

function toggleStatus(btn) {
  const currentStatus = btn.innerText;
  const nextIndex = (statusFlow.indexOf(currentStatus) + 1) % statusFlow.length;
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