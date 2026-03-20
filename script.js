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