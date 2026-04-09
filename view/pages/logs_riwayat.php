<?php
// `index.php` sudah memulai session; jangan panggil session_start() lagi
// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header('Location: index.php?page=login&pesan=gagal');
    exit;
}

require 'config/koneksi.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Log Aktivitas & Riwayat Transaksi - Smart Parking</title>
    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <h1>📜 Log Aktivitas & Rekap Riwayat Transaksi</h1>
            <p style="margin: 5px 0; opacity: 0.9;">Smart Parking System</p>
        </div>
        <div class="user-info">
            <p style="margin: 0 0 10px 0;"><?php echo $_SESSION['username']; ?> (<?php echo ucfirst($_SESSION['level']); ?>)</p>
            <a href="/Parkir/index.php?page=logout" class="logout-btn">Logout</a>
        </div>
    </div>

    <!-- Alert -->
    <div id="alertBox" class="alert"></div>

    <!-- Filter Tanggal -->
    <div class="section">
        <h2>🔍 Filter Berdasarkan Tanggal</h2>
        <div class="filter-group">
            <label>
                Tanggal Mulai:
                <input type="date" id="startDate" required>
            </label>
            <label>
                Tanggal Selesai:
                <input type="date" id="endDate" required>
            </label>
            <button onclick="loadLogs()">🔎 Tampilkan Log</button>
        </div>
    </div>

    <!-- Statistics -->
    <div class="stats" id="statsContainer">
        <div class="stat-card checkin">
            <div class="stat-label">Check-In</div>
            <div class="stat-number" id="countCheckin">0</div>
        </div>
        <div class="stat-card checkout">
            <div class="stat-label">Check-Out</div>
            <div class="stat-number" id="countCheckout">0</div>
        </div>
        <div class="stat-card payment">
            <div class="stat-label">Pembayaran</div>
            <div class="stat-number" id="countPayment">0</div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="section">
        <h2>📋 Riwayat Log Aktivitas</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                        <th>Card ID / Kendaraan</th>
                        <th>No Polisi</th>
                        <th>Durasi</th>
                        <th>Biaya</th>
                        <th>Petugas</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="logsTableBody">
                    <tr><td colspan="8" class="empty-message">Silakan pilih tanggal dan tampilkan log</td></tr>
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

function showAlert(message, type = 'info') {
    const alertEl = document.getElementById('alertBox');
    alertEl.textContent = message;
    alertEl.className = 'alert ' + type;
    alertEl.style.display = 'block';
    setTimeout(() => { alertEl.style.display = 'none'; }, 5000);
}

function loadLogs() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    if (!startDate || !endDate) {
        showAlert('Tanggal harus diisi!', 'error');
        return;
    }

    if (startDate > endDate) {
        showAlert('Tanggal mulai tidak boleh lebih besar dari tanggal selesai!', 'error');
        return;
    }

    fetch('../../controller/get_logs.php?start_date=' + startDate + '&end_date=' + endDate)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                renderLogs(data.data);
                showAlert('Total ' + data.total + ' log ditemukan', 'success');
            } else {
                showAlert(data.message, 'error');
                document.getElementById('logsTableBody').innerHTML = '<tr><td colspan="8" class="empty-message">' + data.message + '</td></tr>';
            }
        })
        .catch(error => {
            showAlert('Error: ' + error, 'error');
        });
}

function renderLogs(logs) {
    let html = '';
    let countCheckin = 0, countCheckout = 0, countPayment = 0;

    if (logs.length === 0) {
        document.getElementById('logsTableBody').innerHTML = '<tr><td colspan="8" class="empty-message">Tidak ada log pada tanggal yang dipilih</td></tr>';
        return;
    }

    logs.forEach((log, idx) => {
        const actionBadgeClass = 'action-' + log.action.toLowerCase();
        const durasiDisplay = log.duration ? Math.floor(log.duration / 60) + ' jam ' + (log.duration % 60) + ' menit' : '-';
        const biayaDisplay = log.fee ? 'Rp' + number_format(log.fee) : '-';
        const statusDisplay = log.transaksi_status ? '<span class="status-badge status-' + log.transaksi_status + '">' + log.transaksi_status + '</span>' : '';

        // Count actions
        if (log.action === 'CHECKIN') countCheckin++;
        else if (log.action === 'CHECKOUT') countCheckout++;
        else if (log.action === 'PAYMENT' || log.action === 'GATE_OPEN') countPayment++;

        html += `
            <tr>
                <td>${idx + 1}</td>
                <td>${formatDateTime(log.created_at)}</td>
                <td><span class="action-badge ${actionBadgeClass}">${log.action}</span></td>
                <td><strong>${log.card_id || '-'}</strong> ${statusDisplay}</td>
                <td><strong>${row.nopol ?? '-'}</strong></td>
                <td>${durasiDisplay}</td>
                <td>${biayaDisplay}</td>
                <td>${log.petugas_username || '-'}</td>
                <td><small>${log.message || '-'}</small></td>
            </tr>
        `;
    });

    document.getElementById('logsTableBody').innerHTML = html;
    document.getElementById('countCheckin').textContent = countCheckin;
    document.getElementById('countCheckout').textContent = countCheckout;
    document.getElementById('countPayment').textContent = countPayment;
}

function formatDateTime(dateString) {
    const date = new Date(dateString.replace(' ', 'T'));
    return date.toLocaleString('id-ID');
}

function number_format(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
</script>
</body>
</html>