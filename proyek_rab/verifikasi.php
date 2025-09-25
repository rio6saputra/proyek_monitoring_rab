<?php 
include 'includes/layout/header.php'; 

// Keamanan tambahan: Pastikan hanya admin yang bisa mengakses halaman ini
if ($_SESSION['user_role'] !== 'admin') {
    // Alihkan ke halaman input jika bukan admin
    header("Location: input_rab.php");
    exit;
}
?>

<div class="content-card">
    <div class="card-header">
        <h3>Verifikasi Anggaran</h3>
        <p>Periksa dan berikan persetujuan untuk anggaran yang telah diajukan.</p>
    </div>

    <div class="card-body">
        <div id="filter-verifikasi-container" style="margin-bottom: 20px;">
            <label for="filter-bagian-verifikasi" style="font-weight: 600; margin-right: 10px;">Saring berdasarkan Bagian:</label>
            <select id="filter-bagian-verifikasi" style="min-width: 300px; padding: 8px; border-radius: var(--border-radius);">
                <option value="all">-- Tampilkan Semua Pengajuan --</option>
            </select>
        </div>
        
        <h4>Daftar Pengajuan Menunggu Verifikasi</h4>
        <div id="daftar-pengajuan-container">
            <p>Memuat daftar pengajuan...</p>
        </div>
    </div>
</div>

<div id="detail-verifikasi-container" style="margin-top: 30px; display: none;">
    <div class="content-card">
        <div class="card-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 id="detail-verifikasi-title">Detail Anggaran</h3>
                <div id="verifikasi-action-buttons">
                    <button id="btn-tolak" class="btn-secondary" style="background-color: #dc3545;">Tolak Anggaran</button>
                    <button id="btn-setujui" class="btn-primary" style="background-color: #28a745;">Setujui Anggaran</button>
                </div>
            </div>
        </div>
        <div class="card-body">
             <div id="rab-container-verifikasi"></div>
        </div>
    </div>
</div>


<script type="module" src="js/verifikasi.js"></script>

<?php include 'includes/layout/footer.php'; ?>