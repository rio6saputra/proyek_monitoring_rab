<?php 
// laporan.php (Versi Revisi - Tampilkan Semua Lalu Filter)
include 'includes/layout/header.php'; 
?>

<div class="content-card">
    <div class="card-header">
        <h3>Laporan Rancangan Anggaran Biaya</h3>
        <p>Menampilkan keseluruhan data RAB. Gunakan filter untuk menyaring data yang ditampilkan.</p>
    </div>
    <div class="card-body">
        
        <div id="laporan-filter-container" style="display: flex; gap: 20px; margin-bottom: 20px; align-items: center;">
            <div>
                <label for="filter-biro" style="display: block; font-weight: 600; margin-bottom: 5px;">Filter Biro</label>
                <select id="filter-biro" class="filter-dropdown" data-level="biro" style="min-width: 250px;">
                    <option value="all">-- Semua Biro --</option>
                </select>
            </div>
            <div>
                <label for="filter-bagian" style="display: block; font-weight: 600; margin-bottom: 5px;">Filter Bagian</label>
                <select id="filter-bagian" class="filter-dropdown" data-level="bagian" style="min-width: 250px;">
                    <option value="all">-- Semua Bagian --</option>
                </select>
            </div>
        </div>
        <hr>

        <div id="laporan-container">
            <p>Memuat seluruh data anggaran...</p>
        </div>
    </div>
</div>

<script type="module" src="js/laporan.js"></script>

<?php include 'includes/layout/footer.php'; ?>