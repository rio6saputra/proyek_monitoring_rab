<?php include 'includes/layout/header.php'; ?>

<div class="content-card">
    <div class="card-header">
        <h3>Input Rancangan Anggaran Biaya</h3>
        <p>Silakan mulai dengan memilih kegiatan untuk menampilkan detail anggaran.</p>
    </div>
    <div id="action-bar" class="action-bar">
        <div class="action-bar-content">
            <div id="status-display" class="status-display"></div>
            <div>
                <button id="revisi-anggaran-btn" class="btn-secondary" style="display: none; margin-right: 10px;">Revisi Anggaran</button>
                <button id="submit-anggaran-btn" class="btn-primary" style="display: none;">Submit Anggaran</button>
            </div>
        </div>
    </div>  
    <div class="card-body">
        <div class="selection-steps">
            <label>Kegiatan</label>
            <div>
                <button id="pilih-kegiatan-btn" class="btn-primary">1. Pilih Kegiatan</button>
                <span id="kegiatan-terpilih" class="selection-display"></span>
            </div>
            
            <label id="kro-label" style="display: none;">KRO</label>
            <div id="kro-controls" style="display: none;">
                <button id="pilih-kro-btn" class="btn-primary">2. Pilih KRO</button>
                <span id="kro-terpilih" class="selection-display"></span>
            </div>
        </div>
        <hr>

        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <div id="bagian-filter-container" style="display: none; margin-bottom: 15px;">
            <label for="filter-bagian" style="font-weight: 600; margin-right: 10px;">Filter per Bagian:</label>
            <select id="filter-bagian" style="padding: 8px; border-radius: 8px; border: 1px solid #ccc; min-width: 250px;">
                <option value="all">-- Tampilkan Semua Bagian --</option>
            </select>
        </div>
        <?php endif; ?>
        <hr>
        <div id="search-container" style="display: none; margin-bottom: 15px;">
            <input type="search" id="search-input" placeholder="🔍 Cari berdasarkan uraian...">
        </div>
        <h4>Detail Anggaran</h4>
        <div id="rab-container">
            <p>Detail akan muncul di sini setelah Kegiatan dan KRO dipilih.</p>
        </div>
    </div>
</div>

<div id="selection-modal" class="modal-overlay">
    <div class="modal-content">
        <button id="selection-modal-close" class="modal-close-btn">&times;</button>
        <h3 id="selection-modal-title"></h3>
        <div id="selection-modal-body" class="selection-list"></div>
    </div>
</div>
<div id="akun-modal" class="modal-overlay">
    <div class="modal-content">
        <button id="modal-close" class="modal-close-btn">&times;</button>
        <h3 id="modal-title">Tambah Anggaran Akun</h3>
        <p id="modal-path" style="font-size: 0.9em; color: #555;"></p>
        <hr>
        <div id="modal-body"></div>
    </div>
</div>
<div id="edit-modal" class="modal-overlay">
    <div class="modal-content">
        <button id="edit-modal-close" class="modal-close-btn">&times;</button>
        <h3 id="edit-modal-title">Edit Detail Anggaran</h3>
        <p id="edit-modal-path" style="font-size: 0.9em; color: #555;"></p>
        <hr>
        <div id="edit-modal-body">
            <div id="edit-form-container-root"></div>
        </div>
        <hr>
        <button type="button" id="update-detail-btn" class="btn-primary">Simpan Perubahan</button>
    </div>
</div>
<div id="edit-akun-modal" class="modal-overlay">
    <div class="modal-content">
        <button id="edit-akun-modal-close" class="modal-close-btn">&times;</button>
        <h3 id="edit-akun-modal-title">Edit Kode Akun</h3>
        <p id="edit-akun-modal-path" style="font-size: 0.9em; color: #555;"></p>
        <hr>
        <div id="edit-akun-modal-body">
            <div id="edit-akun-form-container"></div>
        </div>
        <hr>
        <button type="button" id="update-akun-btn" class="btn-primary">Simpan Perubahan</button>
    </div>
</div>
<div id="context-menu" class="context-menu" style="display: none;">
    <ul class="context-menu-list">
        <li data-action="tambah-akun" style="display:none;">Tambah Akun</li>
        <li data-action="tambah-detail" style="display:none;">Tambah Detail</li>
        <li class="separator"></li>
        <li data-action="edit">Edit</li>
        <li data-action="hapus">Hapus</li>
        <li class="separator"></li>
        <li data-action="cetak">Cetak</li>
    </ul>
</div>
<div id="verification-modal" class="modal-overlay">
    <div class="modal-content">
        <button id="verification-modal-close" class="modal-close-btn">&times;</button>
        <h3 id="verification-modal-title"></h3>
        <hr>
        <div id="verification-modal-body"></div>
    </div>
</div>

<?php include 'includes/layout/footer.php'; ?>