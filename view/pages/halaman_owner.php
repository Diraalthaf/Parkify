<?php
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'owner') {
  header('Location: index.php?page=login&pesan=gagal');
  exit;
}

require 'config/koneksi.php';

?>
<!DOCTYPE html>
<html>
<head>
  <title>Dashboard Owner - Parkify</title>
  <link rel="stylesheet" href="assets/css/style.css?v=3">
  
</head>
<body>
<div class="container">
  <div class="header">
    <div>
      <h1>Owner Dashboard - Parkify</h1>
      <p style="margin: 5px 0; opacity: 0.9;">Rekap & Laporan Parkir</p>
    </div>
    <div style="text-align: right;">
      <p style="margin: 0 0 10px 0;"><?php echo $_SESSION['username']; ?></p>
      <a href="index.php?page=logout" class="logout-btn" onclick="return confirmLogout()">Logout</a>
    </div>
  </div>

  <!-- Filter Tanggal -->
  <div class="section">
    <h2>Filter Rekap Berdasarkan Tanggal</h2>
    <div class="filter-group">
      <label>
        Tanggal Mulai:
        <input type="date" id="startDate" required>
      </label>
      <label>
        Tanggal Selesai:
        <input type="date" id="endDate" required>
      </label>
      <button onclick="loadRecap()">Tampilkan Rekap</button>
    </div>
  </div>

  <!-- Statistics -->
  <div class="stats" id="statsContainer">
    <div class="stat-card transactions">
      <div class="stat-label">Total Transaksi</div>
      <div class="stat-number" id="countTransactions">0</div>
    </div>
    <div class="stat-card revenue">
      <div class="stat-label">Total Revenue</div>
      <div class="stat-number" id="totalRevenue">Rp0</div>
    </div>
    <div class="stat-card duration">
      <div class="stat-label">Rata-rata Durasi</div>
      <div class="stat-number" id="avgDuration">0 jam</div>
    </div>
  </div>

  <!-- Detail Transactions -->
  <div class="section">
    <h2>Detail Transaksi Parkir</h2>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Card ID</th>
            <th>Waktu Check-In</th>
            <th>Waktu Check-Out</th>
            <th>Durasi Parkir</th>
            <th>Biaya</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="recapTableBody">
          <tr><td colspan="7" class="empty-message">Silakan pilih tanggal dan tampilkan rekap</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Set tanggal hari ini sebagai default
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('startDate').value = today;
  document.getElementById('endDate').value = today;
});

function loadRecap() {
  const startDate = document.getElementById('startDate').value;
  const endDate = document.getElementById('endDate').value;

  if (!startDate || !endDate) {
    alert('Tanggal harus diisi!');
    return;
  }

  if (startDate > endDate) {
    alert('Tanggal mulai tidak boleh lebih besar dari tanggal selesai!');
    return;
  }

  // Query ke database untuk ambil transaksi DONE dalam range tanggal
  const startDateTime = startDate + ' 00:00:00';
  const endDateTime = endDate + ' 23:59:59';

  fetch('controller/get_recap.php?start_date=' + startDate + '&end_date=' + endDate)
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        renderRecap(data.data, data.stats);
      } else {
        alert(data.message);
        document.getElementById('recapTableBody').innerHTML = '<tr><td colspan="7" class="empty-message">' + data.message + '</td></tr>';
      }
    })
    .catch(error => {
      alert('Error: ' + error);
    });
}
function confirmLogout() {
    return confirm("Apakah Anda yakin ingin logout?");
}

function renderRecap(transactions, stats) {
  let html = '';

  if (transactions.length === 0) {
    document.getElementById('recapTableBody').innerHTML = '<tr><td colspan="7" class="empty-message">Tidak ada transaksi pada tanggal yang dipilih</td></tr>';
    document.getElementById('countTransactions').textContent = '0';
    document.getElementById('totalRevenue').textContent = 'Rp0';
    document.getElementById('avgDuration').textContent = '0 jam';
    return;
  }

  transactions.forEach((tx, idx) => {
    const durationHours = Math.floor(tx.duration / 60);
    const durationMinutes = tx.duration % 60;
    const durationDisplay = durationHours + ' jam ' + durationMinutes + ' menit';
    const feeDisplay = 'Rp' + numberFormat(tx.fee);

    html += `
      <tr>
        <td>${idx + 1}</td>
        <td><strong>${tx.card_id}</strong></td>
        <td>${formatDateTime(tx.checkin_time)}</td>
        <td>${formatDateTime(tx.checkout_time)}</td>
        <td>${durationDisplay}</td>
        <td><strong>${feeDisplay}</strong></td>
        <td><span class="status-badge status-done">DONE</span></td>
      </tr>
    `;
  });

  document.getElementById('recapTableBody').innerHTML = html;
  document.getElementById('countTransactions').textContent = stats.total_transactions;
  document.getElementById('totalRevenue').textContent = 'Rp' + numberFormat(stats.total_revenue);
  
  const avgDurationHours = Math.floor(stats.avg_duration / 60);
  const avgDurationMinutes = Math.round(stats.avg_duration % 60);
  document.getElementById('avgDuration').textContent = avgDurationHours + ' jam ' + avgDurationMinutes + ' menit';
}

function formatDateTime(dateString) {
  const date = new Date(dateString);
  const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' };
  return date.toLocaleDateString('id-ID', options);
}

function numberFormat(num) {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
</script>

</body>
</html>