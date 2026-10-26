const students = [
  { name: "Demo Student One", roll: "DEMO/2026/001", token: "CAMPUS-DEMO-001" },
  { name: "Demo Student Two", roll: "DEMO/2026/002", token: "CAMPUS-DEMO-002" }
];

const defaultStatus = Object.fromEntries(students.map((student) => [student.token, "OUT"]));
let statuses = JSON.parse(localStorage.getItem("campusPassStatus") || "null") || { ...defaultStatus };
let history = JSON.parse(localStorage.getItem("campusPassHistory") || "[]");
let selectedStudent = null;

function saveDemo() {
  localStorage.setItem("campusPassStatus", JSON.stringify(statuses));
  localStorage.setItem("campusPassHistory", JSON.stringify(history));
}

function renderPasses() {
  const list = document.querySelector("#student-list");
  list.innerHTML = students.map((student) => `
    <article class="student-card"><div class="card-title-row"><div><p class="small-heading">STUDENT PASS</p><h2>${student.name}</h2><p class="roll-number">${student.roll}</p></div><span class="status ${statuses[student.token] === "IN" ? "inside" : "outside"}">${statuses[student.token]}</span></div>
      <div class="qr-frame"><div class="qr-code" data-token="${student.token}"></div></div><p class="pass-help">Show this QR and your college ID at the gate.</p></article>`).join("");
  if (window.QRCode) document.querySelectorAll(".qr-code").forEach((box) => {
    const passLink = new URL("./", window.location.href);
    passLink.searchParams.set("scan", box.dataset.token);
    new QRCode(box, { text: passLink.href, width: 148, height: 148, colorDark: "#1d3551", colorLight: "#ffffff" });
  });
}

function showMessage(text, kind = "info") {
  const message = document.querySelector("#scan-message");
  message.textContent = text;
  message.className = `message ${kind}`;
}

function renderHistory() {
  const target = document.querySelector("#scan-history");
  if (!history.length) { target.innerHTML = '<p class="muted">No scans recorded in this browser yet.</p>'; return; }
  target.innerHTML = `<ul class="history-list">${history.slice(0, 6).map((item) => `<li><span><strong>${item.name}</strong><small>${item.roll} · ${item.time}</small></span><b>${item.movement}</b></li>`).join("")}</ul>`;
}

function findStudent(token) {
  selectedStudent = students.find((student) => student.token === token) || null;
  const result = document.querySelector("#student-result");
  if (!selectedStudent) { result.classList.add("hidden"); showMessage("QR token not found. Scan a sample pass and try again.", "error"); return; }
  document.querySelector("#result-name").textContent = selectedStudent.name;
  document.querySelector("#result-roll").textContent = selectedStudent.roll;
  document.querySelector("#result-status").textContent = statuses[selectedStudent.token];
  document.querySelector("#movement").value = "";
  document.querySelector("#id-check").checked = false;
  result.classList.remove("hidden");
  document.querySelector("#scan-message").className = "message hidden";
}

document.querySelectorAll("[data-page-link]").forEach((link) => link.addEventListener("click", () => {
  const page = link.dataset.pageLink;
  document.querySelectorAll(".page-section").forEach((section) => section.classList.toggle("hidden", section.id !== page));
  document.querySelectorAll(".nav-link").forEach((item) => item.classList.toggle("active", item === link));
}));

document.querySelector("#scan-form").addEventListener("submit", (event) => {
  event.preventDefault();
  const token = document.querySelector("#qr-token").value.trim();
  findStudent(token);
});

document.querySelector("#confirm-form").addEventListener("submit", (event) => {
  event.preventDefault();
  if (!selectedStudent) return;
  const movement = document.querySelector("#movement").value;
  const required = movement === "ENTRY" ? "OUT" : "IN";
  if (statuses[selectedStudent.token] !== required) { showMessage(movement === "ENTRY" ? "This student is already marked inside." : "Record an entry before recording an exit.", "error"); return; }
  statuses[selectedStudent.token] = movement === "ENTRY" ? "IN" : "OUT";
  history.unshift({ name: selectedStudent.name, roll: selectedStudent.roll, movement, time: new Date().toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" }) });
  saveDemo();
  document.querySelector("#result-status").textContent = statuses[selectedStudent.token];
  showMessage(`${selectedStudent.name}: ${movement} recorded. New status: ${statuses[selectedStudent.token]}.`, "success");
  renderHistory(); renderPasses();
});

document.querySelector("#reset-demo").addEventListener("click", () => {
  statuses = { ...defaultStatus }; history = []; saveDemo(); renderHistory(); renderPasses();
  document.querySelector("#student-result").classList.add("hidden"); showMessage("Demo scans have been cleared.", "success");
});

renderPasses();
renderHistory();

const scannedToken = new URLSearchParams(window.location.search).get("scan");
if (scannedToken) {
  document.querySelectorAll(".page-section").forEach((section) => section.classList.toggle("hidden", section.id !== "guard"));
  document.querySelectorAll(".nav-link").forEach((link) => link.classList.toggle("active", link.dataset.pageLink === "guard"));
  document.querySelector("#qr-token").value = scannedToken;
  document.querySelector("#scan-form").requestSubmit();
  history.replaceState({}, "", `${window.location.pathname}#guard`);
}
