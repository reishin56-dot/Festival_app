function goToMenu() {
  const ticket = document.getElementById("ticketInput").value;

  if (ticket.trim() === "") {
    alert("Bitte Ticketnummer eingeben!");
    return;
  }

  // Weiterleitung zur neuen Seite
  window.location.href = "menu.html";
}

function goBack() {
  window.location.href = "index.html";
}