<?php
    require 'auth.php';
    require 'koneksi.php';

    $query = "SELECT * FROM anggota ORDER BY joindate DESC LIMIT 10";
    $result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anggota</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="mobile.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .topbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .logout {
            color: white;
            text-decoration: none;
        }
        .layout {
            display: flex;
            min-height: 100vh;
        }
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
        .content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border: none;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: bold;
            padding: 15px 20px;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .action-buttons .btn {
            margin-right: 5px;
            padding: 5px 10px;
        }
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding: 10px 0;
        }
        .dataTables_info {
            color: #6c757d;
        }
        .modal-content {
            border-radius: 10px;
            border: none;
        }
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }
        .form-group label {
            font-weight: 500;
            margin-bottom: 5px;
            color: #495057;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        .page-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            margin-top: -10px;
        }
    </style>
</head>
<body>

    <?php
    // Display success/error messages
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
        <strong>PRI L</strong>ink
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
        <img src="gmbr/logo.png" class="logo">
            <nav>
                <a href="dashboard.php"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
                <a href="anggota.php" class="active"><i class="bi bi-people"></i><span>Anggota</span></a>
                <a href="pengajuan.php"><i class="bi bi-file-earmark"></i><span>Pengajuan</span></a>
                <a href="pengajuanbunga.php"><i class="bi bi-percent"></i><span>Pengajuan Bunga</span></a>
                <a href="pengajuanlunas.php"><i class="bi bi-check-circle"></i><span>Pengajuan Lunas</span></a>
                <a href="blacklist.php"><i class="bi bi-x-circle"></i><span>Blacklist</span></a>
                <a href="tabungan.php" ><i class="bi bi-wallet2"></i><span>Tabungan</span></a>
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
        <div class="page-title">
            <h3 class="mb-0">Anggota</h3>
        </div>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Daftar Anggota</span>
                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Anggota
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="anggotaTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Anggota</th>
                                <th>Jenis Usaha</th>
                                <th>Status</th>
                                <th>Bergabung Sejak</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>   
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <?php
                                    // Format status
                                    $statusClass = $row['status'] == 'aktif' ? 'status-active' : 'status-inactive';
                                    $statusText = $row['status'] == 'aktif' ? 'Aktif' : 'Nonaktif';
                                    
                                    // Format join date
                                    $joinDate = date('d M Y', strtotime($row['joindate']));
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nama']); ?></td>
                                    <td><?= htmlspecialchars($row['usaha']); ?></td>
                                    <td>
                                        <form action="anggota_status.php" method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="status-badge <?= $statusClass ?> border-0">
                                                <?= $statusText ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td><?= $joinDate ?></td>
                                    <td class="action-buttons">
                                        <!-- View Button -->
                                        <button class="btn btn-info btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewModal<?= $row['id'] ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- View Modal -->
                                <div class="modal fade" id="viewModal<?= $row['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detail Anggota</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama:</label>
                                                            <p class="form-control-static"><?= htmlspecialchars($row['nama']) ?></p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Jenis Usaha:</label>
                                                            <p class="form-control-static"><?= htmlspecialchars($row['usaha']) ?></p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">No Telephone:</label>
                                                            <p class="form-control-static"><?= htmlspecialchars($row['no_telp']) ?></p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Email:</label>
                                                            <p class="form-control-static"><?= htmlspecialchars($row['email']) ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Alamat:</label>
                                                            <p class="form-control-static"><?= htmlspecialchars($row['alamat']) ?></p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">NIK:</label>
                                                            <p class="form-control-static"><?= htmlspecialchars($row['NIK']) ?></p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">TTL:</label>
                                                            <p class="form-control-static"><?= htmlspecialchars($row['ttl']) ?></p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Status:</label>
                                                            <p class="form-control-static">
                                                                <span class="status-badge <?= $statusClass ?>">
                                                                    <?= $statusText ?>
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Bergabung Sejak:</label>
                                                            <p class="form-control-static"><?= $joinDate ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Info -->
                <div class="pagination-container">
                    <div class="dataTables_info">
                        Showing 1 to <?= min(10, mysqli_num_rows($result)) ?> of entries
                    </div>
                    <div>
                        <button class="btn btn-outline-primary btn-sm" id="prevBtn" disabled>
                            Previous
                        </button>
                        <button class="btn btn-outline-primary btn-sm" id="nextBtn">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- ADD MEMBER MODAL -->
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Anggota Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="anggota_add.php" method="POST" id="addMemberForm">
                    <div class="mb-3">
                        <label class="form-label">Nama *</label>
                        <input type="text" class="form-control" name="nama" placeholder="Nama lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Usaha</label>
                        <input type="text" class="form-control" name="usaha" placeholder="Jenis usaha">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No Telephone *</label>
                        <input type="text" class="form-control" name="no_telp" placeholder="Nomor telepon" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" placeholder="Alamat" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NIK</label>
                        <input type="text" class="form-control" name="nik" placeholder="Nomor Induk Kependudukan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">TTL</label>
                        <input type="text" class="form-control" name="ttl" placeholder="Tempat, Tanggal Lahir">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="addMemberForm" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
// Sidebar toggle
document.getElementById("sidebarToggle").onclick = function () {
    document.getElementById("sidebar").classList.toggle("collapsed");
};

// Initialize DataTable
$(document).ready(function() {
    $('#anggotaTable').DataTable({
        "paging": false,
        "searching": true,
        "ordering": true,
        "info": false,
        "language": {
            "search": "Cari:",
            "zeroRecords": "Tidak ada data ditemukan",
            "paginate": {
                "previous": "Sebelumnya",
                "next": "Selanjutnya"
            }
        }
    });
});

// Auto-close alerts after 5 seconds
setTimeout(function() {
    $('.alert').alert('close');
}, 5000);
</script>
<!-- MOBILE BOTTOM NAV -->
<nav class="mobile-nav">
    <a href="dashboard.php">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>
    <a href="anggota.php" class="active">
        <i class="bi bi-people"></i>
        <span>Anggota</span>
    </a>
    <a href="pengajuan.php">
        <i class="bi bi-file-earmark"></i>
        <span>Pengajuan</span>
    </a>
  
    <a href="pengajuanbunga.php">
      <i class="bi bi-percent"></i>
      <span>Pengajuan Bunga</span>
    </a>
  
    <a href="pengajuanlunas.php">
      <i class="bi bi-check-circle"></i>
      <span>Pengajuan Lunas</span>
    </a>

    <a href="blacklist.php">
      <i class="bi bi-x-circle"></i>
      <span>Blacklist</span>
    </a>
  
    <a href="tabungan.php">
        <i class="bi bi-wallet2"></i>
        <span>Tabungan</span>
    </a>

    <a href="libur.php">
      <i class="bi bi-calendar"></i>
      <span>Libur</span>
    </a>
  
    <a href="laporan1.php">
      <i class="bi bi-file-earmark-text"></i>
      <span>Bayar</span>
    </a>
  
    <a href="laporan2.php">
      <i class="bi bi-file-earmark-bar-graph"></i>
      <span>Keuangan</span>
    </a>
  
    <a href="laporan3.php">
      <i class="bi bi-file-earmark-spreadsheet"></i>
      <span>Anggota</span>
    </a>
  
</nav>
</body>
</html>
