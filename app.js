// Add students here. Each student needs a unique roll, passCode, and QR token.
const students = [
  { name: "Demo Student One", roll: "DEMO/2026/001", passCode: "1111", token: "CAMPUS-DEMO-001" },
  { name: "Demo Student Two", roll: "DEMO/2026/002", passCode: "2222", token: "CAMPUS-DEMO-002" }
];

// Demo-only: a code in a public JavaScript file is not secure authentication.
const guardPin = "2468";
const defaultStatuses = Object.fromEntries(students.map((student) => [student.token, "OUT"]));
let statuses = JSON.parse(localStorage.getItem("campusPassStatus") || "null") || { ...defaultStatuses };
let scanHistory = JSON.parse(localStorage.getItem("campusPassHistory") || "[]");
let selectedStudent = null;

function saveDemo() {
  localStorage.setItem("campusPassStatus", JSON.stringify(statuses));
  localStorage.setItem("campusPassHistory", JSON.stringify(scanHistory));
}

function showMessage(id, text, kind) {
  const box = document.getElementById(id);
  box.textContent = text;
  box.className = "message " + (kind || "info");
}

function hideMessage(id) {
  document.getElementById(id).className = "message hidden";
}

function studentSignIn() {
  const form = document.getElementById("student-login-form");
  if (!form) return;
  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const roll = document.getElementById("student-roll").value.trim().toLowerCase();
    const code = document.getElementById("student-code").value.trim();
    const student = students.find((item) => item.roll.toLowerCase() === roll && item.passCode === code);
    if (!student) { showMessage("student-message", "Roll number or pass code is incorrect.", "error"); return; }
    hideMessage("student-message");
    document.getElementById("student-login").classList.add("hidden");
    document.getElementById("student-pass").classList.remove("hidden");
    document.getElementById("student-name").textContent = student.name;
    document.getElementById("student-roll-display").textContent = student.roll;
    document.getElementById("student-status").textContent = statuses[student.token] || "OUT";
    const guardLink = new URL("guard.html", window.location.href);
    guardLink.searchParams.set("scan", student.token);
    document.getElementById("student-qr").innerHTML = "";
    if (window.QRCode) new QRCode(document.getElementById("student-qr"), { text: guardLink.href, width: 148, height: 148, colorDark: "#1d3551", colorLight: "#ffffff" });
  });
  document.getElementById("student-logout").addEventListener("click", () => {
    document.getElementById("student-pass").classList.add("hidden");
    document.getElementById("student-login").classList.remove("hidden");
    form.reset();
  });
}

function renderHistory() {
  const target = document.getElementById("scan-history");
  if (!target) return;
  if (!scanHistory.length) { target.innerHTML = '<p class="muted">No scans recorded in this browser yet.</p>'; return; }
  target.innerHTML = "<ul class=\"history-list\">" + scanHistory.slice(0, 6).map((item) =>
    "<li><span><strong>" + item.name + "</strong><small>" + item.roll + " · " + item.time + "</small></span><b>" + item.movement + "</b></li>"
  ).join("") + "</ul>";
}

function findStudent(rawValue) {
  let token = rawValue.trim();
  const scanMatch = token.match(/[?&]scan=([^&#]+)/);
  if (scanMatch) token = decodeURIComponent(scanMatch[1]);
  selectedStudent = students.find((student) => student.token === token) || null;
  const result = document.getElementById("student-result");
  if (!selectedStudent) { result.classList.add("hidden"); showMessage("scan-message", "QR not found. Scan a valid student pass and try again.", "error"); return; }
  document.getElementById("result-name").textContent = selectedStudent.name;
  document.getElementById("result-roll").textContent = selectedStudent.roll;
  document.getElementById("result-status").textContent = statuses[selectedStudent.token] || "OUT";
  document.getElementById("movement").value = "";
  document.getElementById("id-check").checked = false;
  result.classList.remove("hidden");
  hideMessage("scan-message");
}

function openGuardDashboard() {
  document.getElementById("guard-login").classList.add("hidden");
  document.getElementById("guard-dashboard").classList.remove("hidden");
  renderHistory();
  const pendingToken = new URLSearchParams(window.location.search).get("scan");
  if (pendingToken) {
    document.getElementById("qr-token").value = pendingToken;
    findStudent(pendingToken);
  }
}

function guardSignIn() {
  const form = document.getElementById("guard-login-form");
  if (!form) return;
  const isGuardPage = window.location.pathname.endsWith("guard.html");
  if (isGuardPage && sessionStorage.getItem("campusPassGuard") === "yes") openGuardDashboard();
  form.addEventListener("submit", (event) => {
    event.preventDefault();
    if (document.getElementById("guard-pin").value !== guardPin) { showMessage("guard-message", "Incorrect guard PIN.", "error"); return; }
    sessionStorage.setItem("campusPassGuard", "yes");
    hideMessage("guard-message");
    openGuardDashboard();
  });
  document.getElementById("guard-logout").addEventListener("click", () => {
    sessionStorage.removeItem("campusPassGuard");
    document.getElementById("guard-dashboard").classList.add("hidden");
    document.getElementById("guard-login").classList.remove("hidden");
    form.reset();
  });
  document.getElementById("scan-form").addEventListener("submit", (event) => {
    event.preventDefault();
    findStudent(document.getElementById("qr-token").value);
  });
  document.getElementById("confirm-form").addEventListener("submit", (event) => {
    event.preventDefault();
    if (!selectedStudent) return;
    const movement = document.getElementById("movement").value;
    const requiredStatus = movement === "ENTRY" ? "OUT" : "IN";
    if ((statuses[selectedStudent.token] || "OUT") !== requiredStatus) {
      showMessage("scan-message", movement === "ENTRY" ? "This student is already marked inside." : "Record an entry before an exit.", "error");
      return;
    }
    statuses[selectedStudent.token] = movement === "ENTRY" ? "IN" : "OUT";
    scanHistory.unshift({ name: selectedStudent.name, roll: selectedStudent.roll, movement, time: new Date().toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" }) });
    saveDemo();
    document.getElementById("result-status").textContent = statuses[selectedStudent.token];
    showMessage("scan-message", selectedStudent.name + ": " + movement + " recorded. New status: " + statuses[selectedStudent.token] + ".", "success");
    renderHistory();
  });
  document.getElementById("reset-demo").addEventListener("click", () => {
    statuses = { ...defaultStatuses }; scanHistory = []; saveDemo(); renderHistory();
    document.getElementById("student-result").classList.add("hidden");
    showMessage("scan-message", "Demo scans have been cleared.", "success");
  });
}

studentSignIn();
guardSignIn();
