const unusedLegacySports = [
  ["Bola Sepak", "Lelaki", "⚽"],
  ["Bola Jaring", "Wanita", "🏐"],
  ["Bola Tampar", "Lelaki & Wanita", "🏐"],
  ["Badminton", "Lelaki & Wanita", "🏸"],
  ["Sepak Takraw", "Lelaki", "🏐"],
  ["Skuasy", "Lelaki & Wanita", "🎾"],
  ["Ping Pong", "Lelaki & Wanita", "🏓"],
  ["Petanque", "Lelaki & Wanita", "●"],
  ["Bola Keranjang", "Lelaki & Wanita", "🏀"],
  ["Tenis", "Lelaki & Wanita", "🎾"],
  ["Aerobic Dance", "Wanita", "★"],
];

const sports = [
  ["Bola Sepak", "Lelaki", "BS"],
  ["Petanque", "Lelaki & Wanita", "PTQ"],
  ["Bola Tampar", "Lelaki & Wanita", "BT"],
  ["Bola Jaring", "Wanita", "BJ"],
  ["Sepak Takraw", "Lelaki", "ST"],
  ["Bola Keranjang", "Lelaki & Wanita", "BK"],
  ["Tenis", "Lelaki & Wanita", "TN"],
  ["Skuasy", "Lelaki & Wanita", "SQ"],
  ["Badminton", "Lelaki & Wanita", "BD"],
];

const contingents = [
  "Kolej Matrikulasi Selangor (KMS)",
  "Kolej Matrikulasi Melaka (KMM)",
  "Kolej Matrikulasi Negeri Sembilan (KMNS)",
  "Kolej Matrikulasi Pulau Pinang (KMPP)",
  "Kolej Matrikulasi Perlis (KMP)",
  "Kolej Matrikulasi Kedah (KMK)",
  "Kolej Matrikulasi Perak (KMPk)",
  "Kolej Matrikulasi Johor (KMJ)",
  "Kolej Matrikulasi Labuan (KML)",
  "Kolej Matrikulasi Pahang (KMPh)",
  "Kolej Matrikulasi Kelantan (KMKt)",
  "Kolej Matrikulasi Sarawak (KMSw)",
  "Kolej Matrikulasi Kejuruteraan Kedah (KMKK)",
  "Kolej Matrikulasi Kejuruteraan Pahang (KMKPH)",
  "Kolej Matrikulasi Kejuruteraan Johor (KMKJ)",
  "Kolej Mara Kulim",
  "Kolej Mara Kuala Nerang",
];

const defaultSchedule = [
  {
    date: "Akan dikemaskini",
    time: "-",
    sport: "Bola Sepak",
    match: "Kumpulan A",
    venue: "Padang KMS",
    status: "Draf",
  },
  {
    date: "Akan dikemaskini",
    time: "-",
    sport: "Badminton",
    match: "Pusingan Awal",
    venue: "Dewan",
    status: "Draf",
  },
  {
    date: "Akan dikemaskini",
    time: "-",
    sport: "Bola Jaring",
    match: "Separuh Akhir",
    venue: "Gelanggang Serbaguna",
    status: "Draf",
  },
];

const defaultResults = [];
const storageKey = "kakom-kms-results";

function getResults() {
  try {
    return JSON.parse(localStorage.getItem(storageKey)) || defaultResults;
  } catch {
    return defaultResults;
  }
}

function saveResults(results) {
  localStorage.setItem(storageKey, JSON.stringify(results));
}

function renderSports() {
  const grid = document.querySelector("[data-sports]");
  const select = document.querySelector("[data-sport-select]");

  grid.innerHTML = sports
    .map(([name, category, icon]) => `
      <article class="sport-card">
        <span class="sport-icon">${icon}</span>
        <div>
          <strong>${name}</strong>
          <span>${category}</span>
        </div>
      </article>
    `)
    .join("");

  select.innerHTML = sports
    .map(([name]) => `<option value="${name}">${name}</option>`)
    .join("");
}

function renderContingents() {
  const select = document.querySelector("[data-contingent-select]");

  if (select) {
    select.innerHTML = contingents
      .map((name) => `<option value="${name}">${name}</option>`)
      .join("");
  }

  document.querySelector("[data-total-contingents]").textContent = contingents.length;
}

function renderSchedule() {
  const table = document.querySelector("[data-schedule-table]");
  table.innerHTML = defaultSchedule
    .map((item) => `
      <tr>
        <td>${item.date}</td>
        <td>${item.time}</td>
        <td>${item.sport}</td>
        <td>${item.match}</td>
        <td>${item.venue}</td>
        <td><span class="status">${item.status}</span></td>
      </tr>
    `)
    .join("");

  document.querySelector("[data-total-schedule]").textContent = defaultSchedule.length;
}

function renderResults() {
  const results = getResults();
  const list = document.querySelector("[data-results-list]");
  const medalTable = document.querySelector("[data-medal-table]");
  const totals = Object.fromEntries(
    contingents.map((name, index) => [name, { gold: 0, silver: 0, bronze: 0, index }])
  );

  results.forEach((result) => {
    if (!result.medal) return;
    totals[result.winner] ||= { gold: 0, silver: 0, bronze: 0, index: contingents.length };
    totals[result.winner][result.medal] += 1;
  });

  const medalRows = Object.entries(totals)
    .sort(([, a], [, b]) => (
      (b.gold - a.gold) ||
      (b.silver - a.silver) ||
      (b.bronze - a.bronze) ||
      (a.index - b.index)
    ))
    .map(([team, medal]) => {
      const total = medal.gold + medal.silver + medal.bronze;
      return `
        <tr>
          <td>${team}</td>
          <td>${medal.gold}</td>
          <td>${medal.silver}</td>
          <td>${medal.bronze}</td>
          <td>${total}</td>
        </tr>
      `;
    })
    .join("");

  medalTable.innerHTML = medalRows || `
    <tr>
      <td colspan="5">Belum ada pingat direkodkan.</td>
    </tr>
  `;

  list.innerHTML = results.length
    ? results
        .slice()
        .reverse()
        .map((result) => `
          <article class="result-item">
            <strong>${result.sport} - ${result.category}</strong>
            <span>${result.round} | ${result.winner} | ${result.score}${result.medal ? ` | ${medalLabel(result.medal)}` : ""}</span>
          </article>
        `)
        .join("")
    : `<article class="result-item"><strong>Belum ada keputusan rasmi.</strong><span>Masukkan keputusan melalui borang rekod sementara.</span></article>`;

  document.querySelector("[data-total-results]").textContent = results.length;
}

function medalLabel(value) {
  return {
    gold: "Emas",
    silver: "Perak",
    bronze: "Gangsa",
  }[value] || "";
}

function bindForm() {
  const form = document.querySelector("[data-result-form]");
  const clearButton = document.querySelector("[data-clear-results]");

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const data = new FormData(form);
    const result = Object.fromEntries(data.entries());
    const results = getResults();
    results.push({ ...result, createdAt: new Date().toISOString() });
    saveResults(results);
    form.reset();
    renderResults();
  });

  clearButton.addEventListener("click", () => {
    if (!confirm("Padam semua keputusan sementara pada pelayar ini?")) return;
    localStorage.removeItem(storageKey);
    renderResults();
  });
}

function startCountdown() {
  const countdown = document.querySelector("[data-event-date]");
  const target = new Date(countdown.dataset.eventDate).getTime();
  const day = 24 * 60 * 60 * 1000;
  const hour = 60 * 60 * 1000;
  const minute = 60 * 1000;

  function update() {
    const distance = Math.max(0, target - Date.now());
    document.querySelector("[data-days]").textContent = String(Math.floor(distance / day)).padStart(2, "0");
    document.querySelector("[data-hours]").textContent = String(Math.floor((distance % day) / hour)).padStart(2, "0");
    document.querySelector("[data-minutes]").textContent = String(Math.floor((distance % hour) / minute)).padStart(2, "0");
    document.querySelector("[data-seconds]").textContent = String(Math.floor((distance % minute) / 1000)).padStart(2, "0");
  }

  update();
  setInterval(update, 1000);
}

renderSports();
renderContingents();
renderSchedule();
renderResults();
bindForm();
startCountdown();
