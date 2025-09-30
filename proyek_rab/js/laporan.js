// proyek_rab/js/laporan.js (Versi Refactored - Hanya untuk Render)
const state = {};
const elements = {};

const formatRupiah = (number) => {
    if (number === null || typeof number === 'undefined' || isNaN(number) || number === 0) return '';
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(number);
};

const initializeElements = () => {
    Object.assign(elements, {
        filterBiro: document.getElementById('filter-biro'),
        filterBagian: document.getElementById('filter-bagian'),
        filterKegiatan: document.getElementById('filter-kegiatan'),
        filterKro: document.getElementById('filter-kro'),
        filterStatus: document.getElementById('filter-status'),
        applyFilterBtn: document.getElementById('apply-filter-btn'),
        exportExcelBtn: document.getElementById('export-excel-btn'),
        summaryTotalAnggaran: document.getElementById('summary-total-anggaran'),
        summaryTotalPengajuan: document.getElementById('summary-total-pengajuan'),
        summaryMenungguVerifikasi: document.getElementById('summary-menunggu-verifikasi'),
        tableRenderArea: document.getElementById('table-render-area'),
    });
};

const populateDropdown = (select, data, valueField, textField, defaultText) => {
    if (!select) return;
    select.innerHTML = `<option value="all">-- ${defaultText} --</option>`;
    (data || []).forEach(item => {
        const option = document.createElement('option');
        option.value = item[valueField];
        option.textContent = item[textField];
        select.appendChild(option);
    });
};

const loadAllFilters = async () => {
    try {
        const response = await fetch('api/get_laporan_filters.php').then(res => res.json());
        state.allBagian = response.bagian || [];
        if (elements.filterBiro) populateDropdown(elements.filterBiro, response.biro, 'biro_id', 'nama_biro', 'Semua Biro');
        if (elements.filterBagian) populateDropdown(elements.filterBagian, state.allBagian, 'bagian_id', 'nama_bagian', 'Semua Bagian');
        populateDropdown(elements.filterKegiatan, response.kegiatan, 'kegiatan_id', 'nama_kegiatan', 'Semua Kegiatan');
        populateDropdown(elements.filterKro, response.kro, 'kro_id', 'nama_kro', 'Semua KRO');
        populateDropdown(elements.filterStatus, response.status, 'value', 'text', 'Semua Status');
    } catch (error) { console.error("Gagal memuat filter:", error); }
};

const renderSummaryCards = (summaryData) => {
    if (!summaryData) {
        elements.summaryTotalAnggaran.textContent = 'Rp 0';
        elements.summaryTotalPengajuan.textContent = '0 Pengajuan';
        elements.summaryMenungguVerifikasi.textContent = '0 Pengajuan';
        return;
    }
    elements.summaryTotalAnggaran.textContent = formatRupiah(summaryData.total_anggaran) || 'Rp 0';
    elements.summaryTotalPengajuan.textContent = `${summaryData.total_pengajuan || 0} Pengajuan`;
    elements.summaryMenungguVerifikasi.textContent = `${summaryData.menunggu_verifikasi || 0} Pengajuan`;
};

// --- BAGIAN RENDER HTML (REKURSIF & JAUH LEBIH SEDERHANA) ---

const renderDetailsTreeRecursive = (items) => {
    let html = '';
    (items || []).forEach(item => {
        if (!item) return;
        const isRevised = item.status_revisi !== 'ORIGINAL';
        const safeJsonParse = (jsonString) => {
            if (typeof jsonString === 'object' && jsonString !== null) return jsonString;
            if (!jsonString || typeof jsonString !== 'string') return [];
            try { return JSON.parse(jsonString); } catch (e) { return []; }
        };

        const volumeHtml = safeJsonParse(item.volume_data).map(v => `<div>${v.volume} ${v.satuan}</div>`).join('');
        const statusDisplayHtml = isRevised ? `<span class="col-status status-revisi">REVISI</span>` : '';
        
        const volumeRevisiHtml = safeJsonParse(item.volume_data_revisi || item.volume_data || '[]').map(v => `<div>${v.volume} ${v.satuan}</div>`).join('');
        const hargaRevisiText = formatRupiah(item.harga_satuan_revisi || item.harga_satuan);
        const totalRevisiText = formatRupiah(item.total_biaya_revisi || item.total_biaya);

        html += `
            <div class="rab-table-row level-detail">
                <div class="col-aksi"></div> <div class="col-mak"></div>
                <div class="col-uraian">${item.uraian_pekerjaan || ''}</div>
                <div class="col-volume">${volumeHtml}</div>
                <div class="col-harga-satuan">${formatRupiah(item.harga_satuan)}</div>
                <div class="col-jumlah">${formatRupiah(item.total_biaya)}</div>
                <div class="col-volume col-revisi">${volumeRevisiHtml}</div>
                <div class="col-harga-satuan col-revisi">${hargaRevisiText}</div>
                <div class="col-jumlah col-revisi">${totalRevisiText}</div>
                <div class="col-catatan-revisi">${isRevised ? (item.catatan_revisi || '') : ''}</div>
                <div class="col-status-container">${statusDisplayHtml}</div>
            </div>`;

        if (item.children && item.children.length > 0) {
            html += renderDetailsTreeRecursive(item.children);
        }
    });
    return html;
};

const renderHierarchyRecursive = (nodes, levelClass) => {
    let html = '';
    (nodes || []).forEach(node => {
        // Render baris untuk node saat ini
        html += `
            <div class="rab-table-row ${levelClass}">
                <div class="col-aksi"></div>
                <div class="col-mak">${node.kode || node.no_ro || node.id || ''}</div>
                <div class="col-uraian">${node.nama || node.nama_kro || ''}</div>
                <div></div><div></div><div class="col-jumlah">${formatRupiah(node.total_anggaran)}</div>
                <div></div><div></div><div class="col-jumlah col-revisi">${formatRupiah(node.total_anggaran_revisi)}</div>
                <div></div><div class="col-status-container"></div>
            </div>`;

        // Render data akun (rincian) jika ada
        (node.saved_data || []).forEach(entry => {
            html += `
                <div class="rab-table-row level-akun">
                    <div class="col-aksi"></div>
                    <div class="col-mak">${entry.kode_akun}</div>
                    <div class="col-uraian">${entry.uraian_akun}</div>
                    <div></div><div></div><div class="col-jumlah">${formatRupiah(entry.total_anggaran)}</div>
                    <div></div><div></div><div class="col-jumlah col-revisi">${formatRupiah(entry.total_anggaran_revisi)}</div>
                    <div></div><div class="col-status-container"></div>
                </div>`;
            if (entry.detailsTree && entry.detailsTree.length > 0) {
                html += renderDetailsTreeRecursive(entry.detailsTree);
            }
        });
        
        // Panggil fungsi ini lagi untuk anak-anaknya dengan level class yang sesuai
        if (node.children && node.children.length > 0) {
            let nextLevelClass = '';
            if (levelClass === 'level-kegiatan') nextLevelClass = 'level-ro';
            else if (levelClass === 'level-ro') nextLevelClass = 'level-komponen';
            else if (levelClass === 'level-komponen') nextLevelClass = 'level-sub';
            else if (levelClass === 'level-sub') nextLevelClass = 'level-sub2';
            html += renderHierarchyRecursive(node.children, nextLevelClass);
        }
    });
    return html;
};

const renderHierarchyReport = (hierarchyData) => {
    if (!hierarchyData || hierarchyData.length === 0) {
        elements.tableRenderArea.innerHTML = '<p>Tidak ada data yang cocok dengan filter yang dipilih.</p>';
        return;
    }
    
    // Memulai proses render rekursif dari level paling atas (KRO/Kegiatan)
    const tableBodyHtml = renderHierarchyRecursive(hierarchyData, 'level-kegiatan');

    elements.tableRenderArea.innerHTML = `
        <div class="rab-table">
            <div class="rab-table-header">
                <div class="col-aksi"></div><div class="col-mak">MAK</div><div class="col-uraian">Uraian</div>
                <div class="col-volume">Volume</div><div>Harga Satuan</div><div class="col-jumlah">Jumlah</div>
                <div class="col-volume">Volume (Rev)</div><div>Harga (Rev)</div><div class="col-jumlah">Jumlah (Rev)</div>
                <div>Catatan Revisi</div><div>Status</div>
            </div>
            <div class="rab-table-body">${tableBodyHtml}</div>
        </div>`;
};

const loadReportData = async () => {
    elements.tableRenderArea.innerHTML = '<p>Memuat data laporan...</p>';
    const activeFilters = {
        biro_id: elements.filterBiro?.value,
        bagian_id: elements.filterBagian?.value,
        kegiatan_id: elements.filterKegiatan.value,
        kro_id: elements.filterKro.value,
        status_anggaran: elements.filterStatus.value,
    };
    Object.keys(activeFilters).forEach(key => (activeFilters[key] === 'all' || !activeFilters[key]) && delete activeFilters[key]);
    const params = new URLSearchParams(activeFilters);

    try {
        const response = await fetch(`api/get_laporan_data.php?${params.toString()}`).then(res => {
            if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
            return res.json();
        });

        if (response.status === 'error') throw new Error(response.message);
        
        renderSummaryCards(response.summary);
        // Langsung render data yang sudah diolah oleh backend
        renderHierarchyReport(response.hierarchy);

    } catch (error) {
        console.error('Gagal memuat data laporan:', error);
        elements.tableRenderArea.innerHTML = `<p style="color:red;">Gagal memuat data laporan. Cek konsol browser (F12) untuk detail.</p>`;
        renderSummaryCards(null);
    }
};

const setupEventListeners = () => {
    if (elements.filterBiro) {
        elements.filterBiro.addEventListener('change', () => {
            const selectedBiroId = elements.filterBiro.value;
            const filteredBagian = (selectedBiroId === 'all') ? state.allBagian : state.allBagian.filter(b => b.biro_id == selectedBiroId);
            populateDropdown(elements.filterBagian, filteredBagian, 'bagian_id', 'nama_bagian', 'Semua Bagian');
        });
    }
    elements.applyFilterBtn.addEventListener('click', loadReportData);
    if (elements.exportExcelBtn) {
        elements.exportExcelBtn.addEventListener('click', () => {
            const activeFilters = {
                biro_id: elements.filterBiro?.value,
                bagian_id: elements.filterBagian?.value,
                kegiatan_id: elements.filterKegiatan.value,
                kro_id: elements.filterKro.value,
                status_anggaran: elements.filterStatus.value,
            };
            Object.keys(activeFilters).forEach(key => (activeFilters[key] === 'all' || !activeFilters[key]) && delete activeFilters[key]);
            const params = new URLSearchParams(activeFilters);
            window.open(`api/export_laporan.php?${params.toString()}`, '_blank');
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    initializeElements();
    setupEventListeners();
    loadAllFilters();
    loadReportData();
});