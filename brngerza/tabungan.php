<?php
require 'auth.php';
require 'koneksi.php';

// Get all tabungan records
$query = "SELECT t.*, a.nama, a.usaha 
          FROM tabungan t 
          JOIN anggota a ON t.anggota_id = a.id 
          ORDER BY t.tanggal_setor DESC 
          LIMIT 10";
$result = mysqli_query($koneksi, $query);

// Get total count
$queryCount = "SELECT COUNT(*) as total FROM tabungan";
$resultCount = mysqli_query($koneksi, $queryCount);
$rowCount = mysqli_fetch_assoc($resultCount);
$totalEntries = $rowCount['total'];

// Get grand total savings
$queryTotal = "SELECT SUM(jumlah) as grand_total FROM tabungan";
$resultTotal = mysqli_query($koneksi, $queryTotal);
$rowTotal = mysqli_fetch_assoc($resultTotal);
$grandTotal = $rowTotal['grand_total'] ?? 0;

// Get active members for dropdown
$queryAnggota = "SELECT * FROM anggota WHERE status = 'aktif' ORDER BY nama";
$resultAnggota = mysqli_query($koneksi, $queryAnggota);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabungan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; }
        .topbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; padding: 15px 20px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .brand { font-size: 1.5rem; font-weight: bold; }
        .logout { color: white; text-decoration: none; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px 0;
            transition: all 0.3s;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar .logo {
            width: 80%;
            margin: 0 auto 20px;
            display: block;
            border-radius: 10px;
        }
        .sidebar.collapsed .logo {
            width: 50px;
        }
        .sidebar nav a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        .sidebar nav a:hover {
            background: #34495e;
            color: #fff;
            border-left-color: #667eea;
        }
        .sidebar nav a.active {
            background: #34495e;
            border-left-color: #667eea;
            font-weight: 500;
        }
        .sidebar nav a i {
            margin-right: 10px;
            font-size: 1.2rem;
            min-width: 24px;
        }
        .sidebar.collapsed nav a span {
            display: none;
        }
        .sidebar.collapsed nav a i {
            margin-right: 0;
            font-size: 1.5rem;
        }
        .content { flex: 1; padding: 20px; overflow-y: auto; }
        .card {
            border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px; border: none;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; border-radius: 10px 10px 0 0 !important;
            font-weight: bold; padding: 15px 20px;
        }
        .summary-card {
            border-radius: 10px; padding: 20px;
            color: white; margin-bottom: 20px;
        }
        .summary-card.total { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .summary-card.count { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .summary-card .label { font-size: 0.9rem; opacity: 0.85; }
        .summary-card .value { font-size: 1.6rem; font-weight: bold; }
        .table th { background-color: #f8f9fa; font-weight: 600; }
        .action-buttons .btn { margin-right: 5px; padding: 5px 10px; }
        .pagination-container {
            display: flex; justify-content: space-between;
            align-items: center; margin-top: 20px; padding: 10px 0;
        }
        .dataTables_info { color: #6c757d; }
        .modal-content { border-radius: 10px; border: none; }
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; border-radius: 10px 10px 0 0;
        }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .btn-primary:hover { background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%); }
        .readonly-field { background-color: #f8f9fa; cursor: not-allowed; }
        .form-label { font-weight: 500; color: #495057; }
        .amount-display { font-weight: 600; color: #155724; }
        .input-group-text { background-color: #e9ecef; }
    </style>
</head>
<body>

<?php
if(isset($_GET['success'])) {
    $message = isset($_GET['message']) ? urldecode($_GET['message']) : 'Operasi berhasil!';
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            ' . htmlspecialchars($message) . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
}
if(isset($_GET['error'])) {
    $message = isset($_GET['message']) ? urldecode($_GET['message']) : 'Terjadi kesalahan!';
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            ' . htmlspecialchars($message) . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
}
?>

<!-- NAVBAR -->
<header class="topbar">
    <div class="brand">
        <strong>PRI Link</strong>
        <button id="sidebarToggle" class="btn btn-sm btn-light ms-3">
            <i class="bi bi-list"></i>
        </button>
    </div>
    <a href="logout.php" class="logout">
        Logout <i class="bi bi-box-arrow-right ms-1"></i>
    </a>
</header>

<div class="layout">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <img src="gmbr/logo.jpeg" class="logo">
            <nav>
                <a href="dashboard.php"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
                <a href="anggota.php"><i class="bi bi-people"></i><span>Anggota</span></a>
                <a href="pengajuan.php"><i class="bi bi-file-earmark"></i><span>Pengajuan</span></a>
                <a href="pengajuanbunga.php"><i class="bi bi-percent"></i><span>Pengajuan Bunga</span></a>
                <a href="pengajuanlunas.php"><i class="bi bi-check-circle"></i><span>Pengajuan Lunas</span></a>
                <a href="blacklist.php"><i class="bi bi-x-circle"></i><span>Blacklist</span></a>
                <a href="tabungan.php" class="active"><i class="bi bi-wallet2"></i><span>Tabungan</span></a>
                <a href="libur.php"><i class="bi bi-calendar"></i><span>Libur</span></a>

                <!-- Dropdown -->
                <div class="nav-dropdown">
                    <a href="#" class="nav-dropdown-toggle" onclick="toggleDropdown(event)">
                        <i class="bi bi-graph-up"></i><span>Laporan</span>
                        <i class="bi bi-chevron-down dropdown-arrow"></i>
                    </a>
                    <div class="nav-dropdown-menu">
                        <a href="laporan1.php"><i class="bi bi-file-earmark-text"></i><span>Bayar</span></a>
                        <a href="laporan2.php"><i class="bi bi-file-earmark-bar-graph"></i><span>Keuangan</span></a>
                        <a href="laporan3.php"><i class="bi bi-file-earmark-spreadsheet"></i><span>Anggota</span></a>
                    </div>
                </div>
            </nav>

            <style>
            .nav-dropdown { position: relative; }

            .nav-dropdown-toggle {
                display: flex;
                align-items: center;
                justify-content: space-between;
                cursor: pointer;
            }

            .dropdown-arrow {
                margin-left: auto;
                transition: transform 0.3s ease;
            }

            .nav-dropdown.open .dropdown-arrow {
                transform: rotate(180deg);
            }

            .nav-dropdown-menu {
                display: none;
                flex-direction: column;
                padding-left: 1rem; /* indent sub-items */
            }

            .nav-dropdown.open .nav-dropdown-menu {
                display: flex;
            }
            </style>

            <script>
            function toggleDropdown(e) {
                e.preventDefault();
                e.currentTarget.closest('.nav-dropdown').classList.toggle('open');
            }
            </script>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="content">

        <!-- Summary Cards -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="summary-card total">
                    <div class="label"><i class="bi bi-cash-stack me-1"></i>Total Tabungan</div>
                    <div class="value">Rp <?= number_format($grandTotal, 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="summary-card count">
                    <div class="label"><i class="bi bi-receipt me-1"></i>Total Setoran</div>
                    <div class="value"><?= number_format($totalEntries) ?> Transaksi</div>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-wallet2 me-2"></i>Data Tabungan Anggota</span>
                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addTabunganModal">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Setoran
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabunganTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Anggota</th>
                                <th>Jenis Usaha</th>
                                <th>Jumlah Setoran</th>
                                <th>Tanggal Setor</th>
                                <th>Keterangan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php if(isset($result) && mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama']) ?></td>
                                        <td><?= htmlspecialchars($row['usaha']) ?></td>
                                        <td class="amount-display">
                                            Rp <?= number_format($row['jumlah'], 0, ',', '.') ?>
                                        </td>
                                        <td><?= date('d F Y', strtotime($row['tanggal_setor'])) ?></td>
                                        <td><?= !empty($row['keterangan']) ? htmlspecialchars($row['keterangan']) : '-' ?></td>
                                        <td class="action-buttons">
                                            <button class="btn btn-info btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewModal<?= $row['id'] ?>">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm"
                                                    onclick="deleteTabungan(<?= $row['id'] ?>)"
                                                    title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- View Modal -->
                                    <div class="modal fade" id="viewModal<?= $row['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Detail Tabungan</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Nama Anggota:</label>
                                                                <p><?= htmlspecialchars($row['nama']) ?></p>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Jenis Usaha:</label>
                                                                <p><?= htmlspecialchars($row['usaha']) ?></p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Jumlah Setoran:</label>
                                                                <p class="amount-display">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></p>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Tanggal Setor:</label>
                                                                <p><?= date('d F Y', strtotime($row['tanggal_setor'])) ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Keterangan:</label>
                                                        <p><?= !empty($row['keterangan']) ? htmlspecialchars($row['keterangan']) : '-' ?></p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Dicatat pada:</label>
                                                        <p><?= date('d F Y H:i', strtotime($row['created_at'])) ?></p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Info -->
                <div class="pagination-container">
                    <div class="dataTables_info">
                        Showing <?= min(10, $totalEntries) ?> of <?= number_format($totalEntries) ?> entries
                    </div>
                    <div>
                        <button class="btn btn-outline-primary btn-sm" id="prevBtn" disabled>Previous</button>
                        <button class="btn btn-outline-primary btn-sm" id="nextBtn" <?= $totalEntries <= 10 ? 'disabled' : '' ?>>Next</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- ADD TABUNGAN MODAL -->
<div class="modal fade" id="addTabunganModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="tabungan_add.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-wallet2 me-2"></i>Tambah Setoran Tabungan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Anggota *</label>
                        <select class="form-control" name="anggota_id" id="tab_anggota_id" required
                                onchange="loadAnggotaInfo(this)">
                            <option value="">-- Pilih Anggota --</option>
                            <?php
                            if(isset($resultAnggota) && mysqli_num_rows($resultAnggota) > 0):
                                while($anggota = mysqli_fetch_assoc($resultAnggota)):
                            ?>
                                <option value="<?= $anggota['id'] ?>"
                                        data-usaha="<?= htmlspecialchars($anggota['usaha']) ?>">
                                    <?= htmlspecialchars($anggota['nama']) ?> - <?= htmlspecialchars($anggota['usaha']) ?>
                                </option>
                            <?php
                                endwhile;
                            endif;
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Usaha</label>
                        <input type="text" class="form-control readonly-field" id="tab_usaha" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Setoran *</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="jumlah"
                                   min="1" required placeholder="contoh: 50000">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Setor *</label>
                        <input type="date" class="form-control" name="tanggal_setor"
                               id="tab_tanggal" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="2"
                                  placeholder="Tambahkan keterangan jika perlu..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
document.getElementById("sidebarToggle").onclick = function () {
    document.getElementById("sidebar").classList.toggle("collapsed");
};

$(document).ready(function() {
    if($.fn.DataTable.isDataTable('#tabunganTable')) {
        $('#tabunganTable').DataTable().destroy();
    }
    $('#tabunganTable').DataTable({
        "paging": false,
        "searching": true,
        "ordering": true,
        "info": false,
        "language": {
            "search": "Cari:",
            "zeroRecords": "Tidak ada data tabungan"
        }
    });

    var today = new Date().toISOString().split('T')[0];
    document.getElementById('tab_tanggal').value = today;
});

function loadAnggotaInfo(select) {
    var selected = select.options[select.selectedIndex];
    document.getElementById('tab_usaha').value = selected.dataset.usaha || '';
}

function deleteTabungan(id) {
    if(confirm('Hapus data setoran ini? Tindakan ini tidak dapat dibatalkan.')) {
        fetch('tabungan_delete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) { alert('Data berhasil dihapus!'); location.reload(); }
            else { alert('Gagal: ' + data.message); }
        });
    }
}
</script>

</body>
</html>