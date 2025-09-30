<?php 
// laporan.php (Versi Final dengan Perbaikan Pemanggilan Script)
include 'includes/layout/header.php'; 
?>

<div class="content-card">
    <div class="card-header">
        <h3>Dashboard Laporan Anggaran</h3>
        <p>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                Gunakan filter di bawah untuk menganalisis data anggaran secara spesifik.
            <?php else: ?>
                Menampilkan laporan untuk bagian Anda. Gunakan filter untuk menyaring lebih lanjut.
            <?php endif; ?>
        </p>
    </div>
    <div class="card-body">
        
        <div id="summary-cards-container" class="summary-cards-container">
            <div class="summary-card">
                <div class="title">Total Anggaran</div>
                <div class="value" id="summary-total-anggaran">Memuat...</div>
            </div>
            <div class="summary-card">
                <div class="title">Jumlah Pengajuan</div>
                <div class="value" id="summary-total-pengajuan">Memuat...</div>
            </div>
            <div class="summary-card warning">
                <div class="title">Menunggu Verifikasi</div>
                <div class="value" id="summary-menunggu-verifikasi">Memuat...</div>
            </div>
        </div>

        <div id="laporan-filter-container" class="filter-grid">
            
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <div class="filter-item">
                <label for="filter-biro">Biro</label>
                <select id="filter-biro" class="filter-dropdown">
                    <option value="all">-- Semua Biro --</option>
                </select>
            </div>
            <div class="filter-item">
                <label for="filter-bagian">Bagian</label>
                <select id="filter-bagian" class="filter-dropdown">
                    <option value="all">-- Semua Bagian --</option>
                </select>
            </div>
            <?php endif; ?>

            <div class="filter-item">
                <label for="filter-kegiatan">Kegiatan</label>
                <select id="filter-kegiatan" class="filter-dropdown">
                    <option value="all">-- Semua Kegiatan --</option>
                </select>
            </div>
            <div class="filter-item">
                <label for="filter-kro">KRO</label>
                <select id="filter-kro" class="filter-dropdown">
                    <option value="all">-- Semua KRO --</option>
                </select>
            </div>
            <div class="filter-item">
                <label for="filter-status">Status Anggaran</label>
                <select id="filter-status" class="filter-dropdown">
                    <option value="all">-- Semua Status --</option>
                </select>
            </div>
            <div class="filter-item action-item">
                 <button id="apply-filter-btn" class="btn-primary">Tampilkan Laporan</button>
            </div>
            <div class="filter-item action-item">
                <button id="export-excel-btn" class="btn-secondary" style="background-color: #1D6F42;">Ekspor ke Excel</button>
            </div>
        </div>
        <hr>

        <div id="laporan-container">
            <div id="table-render-area">
                <p>Memuat data...</p>
            </div>
        </div>
    </div>
</div>

<script type="module" src="js/laporan.js"></script>

<?php include 'includes/layout/footer.php'; ?>