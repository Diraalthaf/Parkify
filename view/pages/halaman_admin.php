<?php
// Cek apakah user admin
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("Location: index.php?page=login&pesan=gagal");
exit;

}

require 'config/koneksi.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin - Parkify</title>
    <link rel="stylesheet" href="assets/css/style.css?v=3">
    <style>
        .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    justify-content: center;
    align-items: center;
}

.modal.show {
    display: flex;
}

.modal-content {
    max-height: 90vh; 
    overflow-y: auto; 
}
</style>

</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <h1>Admin Dashboard - Parkify</h1>
            <p style="margin: 5px 0; opacity: 0.9;">Manajemen Sistem & User</p>
        </div>
        <div class="user-info">
            <p style="margin: 0 0 10px 0;">Selamat datang,<br><strong><?php echo $_SESSION['username']; ?></strong></p>
            <a href="index.php?page=logout" class="logout-btn" onclick="return confirmLogout()">Logout</a>

        </div>
    </div>

    <!-- Alert -->
    <div id="alertBox" class="alert"></div>

    <!-- Statistics -->
    <div class="stats" id="statsContainer">
        <div class="stat-card">
            <div class="stat-label">Total User</div>
            <div class="stat-number" id="countTotalUser">0</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="stat-label">Admin</div>
            <div class="stat-number" id="countAdmin">0</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="stat-label">Owner</div>
            <div class="stat-number" id="countOwner">0</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="stat-label">Petugas</div>
            <div class="stat-number" id="countPegawai">0</div>
        </div>
    </div>

    <!-- USER MANAGEMENT SECTION -->
    <div class="section">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Manajemen User</h2>
            <button class="btn btn-success" onclick="openCreateModal()">+ Tambah User Baru</button>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Level / Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <tr><td colspan="6" class="empty-message">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create/Edit User -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Tambah User Baru</h2>

        <div id="modalAlert" class="alert"></div>

        <form id="userForm">
            <input type="hidden" id="userId" value="">

            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" id="formNama" required>
            </div>

            <div class="form-group">
                <label>Username *</label>
                <input type="text" id="formUsername" required>
            </div>

            <div class="form-group">
                <label>Password <span id="passwordNote"></span>*</label>
                <input type="password" id="formPassword">
            </div>

            <div class="form-group">
                <label>Role / Level *</label>
                <select id="formLevel" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin">Admin</option>
                    <option value="owner">Owner</option>
                    <option value="petugas">Petugas</option>
                </select>
            </div>

            <div style="text-align: right;">
                <button type="button" class="btn" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadUserData();
    // Refresh setiap 5 detik
    setInterval(loadUserData, 5000);

    // Handle form submit
    document.getElementById('userForm').addEventListener('submit', handleUserFormSubmit);
});

function showAlert(message, type = 'success') {
    const alertEl = document.getElementById('alertBox');
    alertEl.textContent = message;
    alertEl.className = 'alert ' + type;
    alertEl.style.display = 'block';
    setTimeout(() => { alertEl.style.display = 'none'; }, 5000);
}

function showModalAlert(message, type = 'error') {
    const alertEl = document.getElementById('modalAlert');
    alertEl.textContent = message;
    alertEl.className = 'alert ' + type;
}

function openCreateModal() {
    const modal = document.getElementById('userModal');

    modal.style.display = 'flex';
    modal.classList.add('show');

    document.getElementById('userId').value = '';
    document.getElementById('modalTitle').textContent = 'Tambah User Baru';
    document.getElementById('formNama').value = '';
    document.getElementById('formUsername').value = '';
    document.getElementById('formPassword').value = '';
    document.getElementById('formPassword').required = true;
    document.getElementById('passwordNote').textContent = '';
    document.getElementById('formLevel').value = '';
    document.getElementById('modalAlert').style.display = 'none';
}

function openEditModal(userId) {
    fetch('controller/user_management.php?action=get&id=' + userId)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const modal = document.getElementById('userModal');
                modal.style.display = 'flex'; // 🔥 penting
                modal.classList.add('show');

                const user = data.data;
                document.getElementById('userId').value = user.id;
                document.getElementById('modalTitle').textContent = 'Edit User: ' + user.nama;
                document.getElementById('formNama').value = user.nama;
                document.getElementById('formUsername').value = user.username;
                document.getElementById('formPassword').value = '';
                document.getElementById('formPassword').required = false;
                document.getElementById('passwordNote').textContent = '(Kosongkan jika tidak ingin mengubah)';
                document.getElementById('formLevel').value = user.level;
            }
        });
}
function confirmLogout() {
    return confirm("Apakah Anda yakin ingin logout?");
}

function closeModal() {
    const modal = document.getElementById('userModal');
    modal.classList.remove('show');
    modal.style.display = 'none'; 
}

function handleUserFormSubmit(e) {
    e.preventDefault();

    const userId = document.getElementById('userId').value;
    const nama = document.getElementById('formNama').value.trim();
    const username = document.getElementById('formUsername').value.trim();
    const password = document.getElementById('formPassword').value;
    const level = document.getElementById('formLevel').value;

    if (!nama || !username || !level) {
        showModalAlert('Semua field harus diisi', 'error');
        return;
    }

    const isCreate = userId === '';

    if (isCreate && !password) {
        showModalAlert('Password harus diisi untuk user baru', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('nama', nama);
    formData.append('username', username);
    if (password) formData.append('password', password);
    formData.append('level', level);

    const url = isCreate 
        ? 'controller/user_management.php?action=create'
        : 'controller/user_management.php?action=update';

    if (!isCreate) {
        formData.append('id', userId);
    }

fetch(url, {
    method: 'POST',
    body: formData
})
.then(response => response.text()) 
.then(res => {
    console.log("RESPONSE:", res); 

    let data;
    try {
        data = JSON.parse(res);
    } catch (e) {
        showModalAlert('Response bukan JSON!', 'error');
        return;
    }

    if (data.status === 'success') {
        showAlert(data.message, 'success');
        closeModal();
        loadUserData();
    } else {
        showModalAlert(data.message, 'error');
    }
})
.catch(error => {
    showModalAlert('Error: ' + error, 'error');
});
}

function deleteUser(userId, userName) {
    if (confirm('Apakah Anda yakin ingin menghapus user "' + userName + '"?')) {
        const formData = new FormData();
        formData.append('id', userId);

        fetch('controller/user_management.php?action=delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert(data.message, 'success');
                loadUserData();
            } else {
                showAlert(data.message, 'error');
            }
        })
        .catch(error => {
            showAlert('Error: ' + error, 'error');
        });
    }
}

function loadUserData() {
    fetch('controller/user_management.php?action=list')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.data.length > 0) {
                let html = '';
                let countAdmin = 0, countOwner = 0, countPegawai = 0;

                data.data.forEach((user, idx) => {
                    let badgeClass = 'status-' + (user.level === 'pengurus' ? 'owner' : user.level);
                    
                    if (user.level === 'admin') countAdmin++;
                    else if (user.level === 'owner' || user.level === 'pengurus') countOwner++;
                    else if (user.level === 'pegawai') countPegawai++;

                    // Map internal role values to friendly display names
                    let displayLevel = '';
                    if (user.level === 'pengurus' || user.level === 'owner') displayLevel = 'OWNER';
                    else if (user.level === 'admin') displayLevel = 'ADMIN';
                    else if (user.level === 'pegawai') displayLevel = 'PETUGAS';
                    else displayLevel = user.level.toUpperCase();

                    html += `
                        <tr>
                            <td>${idx + 1}</td>
                            <td>${user.nama}</td>
                            <td><strong>${user.username}</strong></td>
                            <td><span class="status-badge ${badgeClass}">${displayLevel}</span></td>
                            <td>✅ Aktif</td>
                            <td>
                                <button class="btn btn-warning" onclick="openEditModal(${user.id})">Edit</button>
                                <button class="btn btn-danger" onclick="deleteUser(${user.id}, '${user.nama}')">Hapus</button>
                            </td>
                        </tr>
                    `;
                });

                document.getElementById('userTableBody').innerHTML = html;
                document.getElementById('countTotalUser').textContent = data.data.length;
                document.getElementById('countAdmin').textContent = countAdmin;
                document.getElementById('countOwner').textContent = countOwner;
                document.getElementById('countPegawai').textContent = countPegawai;
            } else {
                document.getElementById('userTableBody').innerHTML = '<tr><td colspan="6" class="empty-message">Belum ada user</td></tr>';
            }
        });
}


window.onclick = function(event) {
    const modal = document.getElementById('userModal');
    if (event.target === modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
    }
}
</script>
</body>
</html>