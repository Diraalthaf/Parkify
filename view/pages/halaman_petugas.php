<?php

if (!isset($_SESSION['username']) || $_SESSION['level'] != 'pegawai') {
    header("Location: index.php?page=login&pesan=gagal");
exit;
}

require 'config/koneksi.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Petugas - Smart Parking</title>
    <link rel="stylesheet" href="assets/css/style.css?v=3">

   

</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <h1>Parkify Dashboard</h1>
            <p style="margin: 5px 0; opacity: 0.9;">Sistem Manajemen Parkir Motor</p>
        </div>
        <div class="user-info">
            <p style="margin: 0 0 10px 0;">Selamat datang,<br><strong><?php echo $_SESSION['username']; ?></strong></p>
            <a href="index.php?page=logout" class="logout-btn" onclick="return confirmLogout()">Logout</a>
        </div>
    </div>

    <!-- Alert Messages -->
    <div id="alertBox" class="alert"></div>

    <!-- Statistics Cards -->
    <div class="stats" id="statsContainer">
        <div class="stat-card active">
            <div class="stat-label">Sedang Parkir</div>
            <div class="stat-number" id="countActive">0</div>
        </div>
        <div class="stat-card pending">
            <div class="stat-label">Menunggu Pembayaran</div>
            <div class="stat-number" id="countPending">0</div>
        </div>
        <div class="stat-card done">
            <div class="stat-label">Transaksi Selesai</div>
            <div class="stat-number" id="countDone">0</div>
        </div>
    </div>

    

    <!-- ACTIVE PARKING (IN) SECTION -->
    <div class="section">
        <h2>Kendaraan Sedang Parkir (Status: IN)</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Card ID</th>
                        <th>No Polisi</th>
                        <th>Waktu Masuk</th>
                        <th>Durasi Parkir</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="activeTableBody">
                    <tr><td colspan="6" class="empty-message">Belum ada kendaraan yang parkir</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- PENDING CHECKOUT (OUT) SECTION -->
    <div class="section">
        <h2>Kendaraan Siap Keluar - Pembayaran (Status: OUT)</h2>
        <p style="color: #7f8c8d; margin-top: 0;">Proses pembayaran dan buka gerbang</p>
        <div id="pendingTableWrapper" class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Card ID</th>
                        <th>No Polisi</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Durasi</th>
                        <th>Biaya</th>
                        <th>Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="pendingTableBody">
                    <tr><td colspan="9" class="empty-message">Tidak ada kendaraan yang siap keluar</td></tr>
                </tbody>
            </table>
        </div>
        <div id="paymentAlert" class="alert"></div>
    </div>

    <!-- HISTORY SECTION -->
    <div class="section">
        <div style="display:flex; justify-content:space-between; align-items:center;">
    <h2>Riwayat Transaksi Selesai (Status: DONE)</h2>

    <a href="controller/export_pdf.php" target="_blank" 
       style="background:#e74c3c; color:white; padding:8px 15px; border-radius:5px; text-decoration:none;">
       Export PDF
    </a>
</div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Card ID</th>
                        <th>No Polisi</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Durasi</th>
                        <th>Biaya</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody">
                    <tr><td colspan="8" class="empty-message">Belum ada transaksi selesai</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Auto-focus ke input check-in
document.addEventListener('DOMContentLoaded', function() {
    const checkinInput = document.getElementById('cardIdCheckin');
    
    if (checkinInput) {
        checkinInput.focus();
    }

    loadAllData();
    setInterval(loadAllData, 3000);
});

// Func: Tampilkan alert
function showAlert(elementId, message, type = 'info') {
    const alertEl = document.getElementById(elementId);
    alertEl.textContent = message;
    alertEl.className = 'alert ' + type;
    alertEl.style.display = 'block';
}

// Func: Clear alert
function clearAlert(elementId) {
    const alertEl = document.getElementById(elementId);
    alertEl.className = 'alert';
    alertEl.style.display = 'none';
}

// Func: Handle Check-In
function handleCheckin() {
    const cardId = document.getElementById('cardIdCheckin').value.trim();
    const nopol = document.getElementById('nopolCheckin').value.trim();
    
    if (!cardId) {
        showAlert('checkinAlert', '⚠️ Silakan tap/input Card ID terlebih dahulu', 'error');
        return;
    }

    fetch('controller/checkin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'card_id=' + encodeURIComponent(cardId) +
      '&nopol=' + encodeURIComponent(nopol)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('checkinAlert', '✅ ' + data.message + ' (Card: ' + data.card_id + ')', 'success');
            document.getElementById('cardIdCheckin').value = '';
            document.getElementById('cardIdCheckin').focus();
            loadAllData();
        } else {
            showAlert('checkinAlert', '❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('checkinAlert', '❌ Error: ' + error, 'error');
    });
}
//func: logout
function confirmLogout() {
    return confirm("Apakah Anda yakin ingin logout?");
}
// Func: Handle Check-Out
function handleCheckout() {
    const cardId = document.getElementById('cardIdCheckout').value.trim();
    
    if (!cardId) {
        showAlert('checkoutAlert', '⚠️ Silakan tap/input Card ID terlebih dahulu', 'error');
        return;
    }

    fetch('controller/checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'card_id=' + encodeURIComponent(cardId)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('checkoutAlert', '✅ ' + data.message + '\n💰 Biaya: ' + data.fee_display, 'success');
            document.getElementById('cardIdCheckout').value = '';
            document.getElementById('cardIdCheckout').focus();
            loadAllData();
        } else {
            showAlert('checkoutAlert', '❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('checkoutAlert', '❌ Error: ' + error, 'error');
    });
}

// Func: Handle Payment & Gate Open
function handlePayment(transaksiId) {
    const amountInput = document.getElementById('amount_' + transaksiId);
    const amount = amountInput.value.trim();

    if (!amount || isNaN(amount)) {
        showAlert('paymentAlert', '⚠️ Silakan input jumlah pembayaran yang valid', 'error');
        return;
    }

    fetch('controller/handle_payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'transaksi_id=' + transaksiId + '&amount_paid=' + amount
    })
    .then(response => response.json())
    .then(data => {
    if (data.status === 'success') {
        showAlert('paymentAlert', 
            '✅ ' + data.message + '\n💵 Kembalian: ' + data.kembalian_display, 
            'success'
        );

        window.open('controller/struk.php?id=' + transaksiId, '_blank');

        loadAllData();
    } else {
        showAlert('paymentAlert', 
            '❌ ' + data.message + '\n(Diperlukan: ' + 
            (data.required_fee ? 'Rp' + data.required_fee.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-') + ')', 
            'error'
        );
    }
})

    
}


// Func: Load all data (active, pending, history)
function loadAllData() {
    clearAlert('paymentAlert');
    
    // Load Active (IN)
    fetch('controller/get_transaksi.php?type=active')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.data.length > 0) {
                let html = '';
                data.data.forEach((row, idx) => {
                    const duration = calculateDuration(new Date(row.checkin_time));
                    html += `
                        <tr>
                            <td>${idx + 1}</td>
                            <td><strong>${row.card_id}</strong></td>
                            <td><strong>${row.nopol ?? '-'}</strong></td>
                            <td>${formatDateTime(row.checkin_time)}</td>
                            <td>${duration}</td>
                            <td><span class="status-badge status-in">${row.status}</span></td>
                        </tr>
                    `;
                });
                document.getElementById('activeTableBody').innerHTML = html;
                document.getElementById('countActive').textContent = data.data.length;
            } else {
                document.getElementById('activeTableBody').innerHTML = '<tr><td colspan="6" class="empty-message">Belum ada kendaraan yang parkir</td></tr>';
                document.getElementById('countActive').textContent = '0';
            }
        });

    // Load Pending (OUT) - SKIP jika ada input biaya yang sedang di-focus
    const focusedElement = document.activeElement;
    const isFocusedOnPaymentInput = focusedElement && focusedElement.id && focusedElement.id.startsWith('amount_');
    
    if (!isFocusedOnPaymentInput) {
        fetch('controller/get_transaksi.php?type=pending')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.data.length > 0) {
                    let html = '';
                    data.data.forEach((row, idx) => {
                        const duration = (row.duration / 60).toFixed(2);
                        html += `
                            <tr>
                                <td>${idx + 1}</td>
                                <td><strong>${row.card_id}</strong></td>
                                <td><strong>${row.nopol ?? '-'}</strong></td>
                                <td>${formatDateTime(row.checkin_time)}</td>
                                <td>${formatDateTime(row.checkout_time)}</td>
                                <td>${parseInt(row.duration / 60)} jam ${row.duration % 60} menit</td>
                                <td><strong>${row.fee_display}</strong></td>
                                <td>
                                    <input type="number" id="amount_${row.id}" placeholder="Rp" min="0" style="width: 120px !important; padding: 8px !important; border: 1px solid #bdc3c7 !important; border-radius: 4px !important; flex: none !important; min-width: auto !important;">
                                </td>
                                <td>
                                    <button onclick="handlePayment(${row.id})" class="btn btn-danger" style="width: auto; padding: 8px 15px;">Buka palang & Cetak Struk</button>
                                </td>
                            </tr>
                        `;
                    });
                    document.getElementById('pendingTableBody').innerHTML = html;
                    document.getElementById('countPending').textContent = data.data.length;
                } else {
                    document.getElementById('pendingTableBody').innerHTML = '<tr><td colspan="9" class="empty-message">Tidak ada kendaraan yang siap keluar</td></tr>';
                    document.getElementById('countPending').textContent = '0';
                }
            });
    }

    // Load History (DONE)
    fetch('controller/get_transaksi.php?type=history')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.data.length > 0) {
                let html = '';
                data.data.forEach((row, idx) => {
                    html += `
                        <tr>
                            <td>${idx + 1}</td>
                            <td><strong>${row.card_id}</strong></td>
                            <td><strong>${row.nopol ?? '-'}</strong></td>
                            <td>${formatDateTime(row.checkin_time)}</td>
                            <td>${formatDateTime(row.checkout_time)}</td>
                            <td>${row.duration_display}</td>
                            <td><strong>${row.fee_display}</strong></td>
                            <td><span class="status-badge status-done">${row.status}</span></td>
                        </tr>
                    `;
                });
                document.getElementById('historyTableBody').innerHTML = html;
                document.getElementById('countDone').textContent = data.data.length;
            } else {
                document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="8" class="empty-message">Belum ada transaksi selesai</td></tr>';
                document.getElementById('countDone').textContent = '0';
            }
        });
}

// Func: Format datetime
function formatDateTime(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
    return date.toLocaleDateString('id-ID', options);
}

// Func: Calculate duration from check-in time until now
function calculateDuration(checkinTime) {
    const now = new Date();
    const diff = now - checkinTime;
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff / (1000 * 60)) % 60);
    return `${hours} jam ${minutes} menit`;
}

// Allow Enter key to submit
const checkinInput = document.getElementById('cardIdCheckin');
if (checkinInput) {
    checkinInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') handleCheckin();
    });
}

const checkoutInput = document.getElementById('cardIdCheckout');
if (checkoutInput) {
    checkoutInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') handleCheckout();
    });
}
</script>
</body>
</html>