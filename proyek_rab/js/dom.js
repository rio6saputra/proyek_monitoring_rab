// js/dom.js (Versi Final dengan State Tampilan Tersimpan)

import state from './state.js';
import * as api from './api.js';

const formatRupiah = (number) => {
    if (number === null || typeof number === 'undefined' || isNaN(number)) {
        return '';
    }
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(number);
};

export const showToast = (status, message) => {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: status,
        title: message
    });
};

let elements = {};

export const initializeElements = () => {
    elements = {
        rabContainer: document.getElementById('rab-container'),
        modal: document.getElementById('akun-modal'),
        modalBody: document.getElementById('modal-body'),
        selectionModal: document.getElementById('selection-modal'),
        selectionModalTitle: document.getElementById('selection-modal-title'),
        selectionModalBody: document.getElementById('selection-modal-body'),
        kroTerpilihDisplay: document.getElementById('kro-terpilih'),
        editModal: document.getElementById('edit-modal'),
        editModalBody: document.getElementById('edit-modal-body'),
        kegiatanTerpilihDisplay: document.getElementById('kegiatan-terpilih'),
        kroLabel: document.getElementById('kro-label'),
        kroControls: document.getElementById('kro-controls'),
        pilihKegiatanBtn: document.getElementById('pilih-kegiatan-btn'),
        pilihKroBtn: document.getElementById('pilih-kro-btn'),
        modalCloseBtn: document.getElementById('modal-close'),
        selectionModalClose: document.getElementById('selection-modal-close'),
        editModalCloseBtn: document.getElementById('edit-modal-close'),
        contextMenu: document.getElementById('context-menu'),
        editAkunModal: document.getElementById('edit-akun-modal'),
        editAkunModalClose: document.getElementById('edit-akun-modal-close'),
        updateAkunBtn: document.getElementById('update-akun-btn'),
        editAkunFormContainer: document.getElementById('edit-akun-form-container'),
        editAkunModalTitle: document.getElementById('edit-akun-modal-title'),
        filterBagian: document.getElementById('filter-bagian'),
        bagianFilterContainer: document.getElementById('bagian-filter-container'),
        searchContainer: document.getElementById('search-container'),
        searchInput: document.getElementById('search-input'),
        updateDetailBtn: document.getElementById('update-detail-btn'),
        actionBar: document.getElementById('action-bar'),
        statusDisplay: document.getElementById('status-display'),
        submitAnggaranBtn: document.getElementById('submit-anggaran-btn'),
        revisiAnggaranBtn: document.getElementById('revisi-anggaran-btn')
    };
};

export {
    elements
};

// [FUNGSI BARU] Untuk merekam baris mana saja yang sedang terbuka
export const getExpandedPaths = () => {
    const expandedRows = document.querySelectorAll('.rab-table-row.expanded');
    return Array.from(expandedRows).map(row => row.dataset.path);
};

// [FUNGSI BARU] Untuk membuka kembali baris yang terekam
export const restoreExpandedState = (paths) => {
    if (!paths || paths.length === 0) return;
    paths.forEach(path => {
        const row = document.querySelector(`.rab-table-row[data-path="${path}"]`);
        if (row) {
            const button = row.querySelector('.toggle-btn');
            // Cek jika tombol ada DAN barisnya belum terbuka (untuk menghindari klik ganda)
            if (button && !row.classList.contains('expanded')) {
                // Memicu klik secara virtual untuk membuka baris dan semua anaknya
                button.click();
            }
        }
    });
};

export const populateBagianFilter = (kegiatanId, kroId) => {
    if (!elements.filterBagian) return;
    api.fetchListBagian(kegiatanId, kroId).then(data => {
        elements.filterBagian.innerHTML = '<option value="all">-- Tampilkan Semua Bagian --</option>';
        data.forEach(bagian => {
            const option = document.createElement('option');
            option.value = bagian.bagian_id;
            option.textContent = bagian.nama_bagian;
            elements.filterBagian.appendChild(option);
        });
    });
};

const createFormGridShell = (formId, entryId, showSubmitButton = true) => {
    const submitButtonHtml = showSubmitButton ?
        '<button type="submit" class="btn-primary">Simpan</button>' :
        '';
    return `
        <form id="${formId}" data-entry-id="${entryId}" class="modal-body-grid-layout">
            <div id="form-header-container"></div>
            <div class="detail-form-grid-wrapper">
                <div class="detail-form-grid">
                    <div class="detail-form-grid-header">Uraian</div>
                    <div class="detail-form-grid-header">Volume / Satuan 1</div>
                    <div class="detail-form-grid-header">Volume / Satuan 2</div>
                    <div class="detail-form-grid-header">Volume / Satuan 3</div>
                    <div class="detail-form-grid-header">Volume / Satuan 4</div>
                    <div class="detail-form-grid-header">Volume / Satuan 5</div>
                    <div class="detail-form-grid-header">Harga Satuan</div>
                    <div class="detail-form-grid-header">Aksi</div>
                    <div id="detail-rows-container" style="display: contents;"></div>
                </div>
            </div>
            <div class="modal-form-footer">
                <div class="total-biaya-display">Total: <span id="total-biaya-otomatis">Rp 0,00</span></div>
                <div>
                    <button type="button" class="add-detail-row-btn btn-secondary">+ Tambah Baris</button>
                    ${submitButtonHtml}
                </div>
            </div>
        </form>
    `;
};

export const createDetailInputRowHTML = (detail = {}, isSubDetail = false, isRevisionMode = false) => {
    const rowClass = isSubDetail ? 'sub-detail-row' : '';
    const detailIdAttr = detail.id_rab_detail ? `data-id-detail="${detail.id_rab_detail}"` : '';
    let volumes_source = [];
    let harga_satuan_source = '';
    if (isRevisionMode) {
        volumes_source = JSON.parse(detail.volume_data_revisi || detail.volume_data || '[]');
        harga_satuan_source = detail.harga_satuan_revisi !== null ? detail.harga_satuan_revisi : detail.harga_satuan;
    } else {
        volumes_source = JSON.parse(detail.volume_data || '[]');
        harga_satuan_source = detail.harga_satuan;
    }
    let volumePairs = '';
    for (let i = 0; i < 5; i++) {
        const vol = volumes_source[i]?.volume || '';
        const sat = volumes_source[i]?.satuan || '';
        volumePairs += `
            <div class="vol-sat-pair">
                <input type="number" class="volume-input" placeholder="Volume ${i+1}" value="${vol}">
                <input type="text" class="satuan-input" placeholder="Satuan ${i+1}" value="${sat}">
            </div>
        `;
    }
    const formattedHarga = harga_satuan_source ? new Intl.NumberFormat('id-ID').format(harga_satuan_source) : '';
    
    // --- PERBAIKAN LOGIKA TOMBOL ---
    // Tombol '+' hanya muncul jika ini BUKAN sub-detail (artinya, ini adalah child, bukan grandchild)
    const addSubButton = !isSubDetail ? '<button type="button" class="add-sub-detail-btn" title="Tambah Sub-Rincian">+</button>' : '';

    return `
        <div class="detail-form-grid-row ${rowClass}" ${detailIdAttr}>
            <textarea class="uraian-input" placeholder="Uraian pekerjaan..." rows="1">${detail.uraian_pekerjaan || ''}</textarea>
            ${volumePairs}
            <input type="text" class="harga-input" placeholder="Harga Satuan" value="${formattedHarga}" inputmode="numeric">
            <div class="aksi-buttons">
                ${addSubButton}
                <button type="button" class="delete-row-btn" title="Hapus Baris">×</button>
            </div>
        </div>
    `;
};

export const updateUiByStatus = (data) => {
    if (!elements.actionBar) return;

    const findFirstEntry = (items) => {
        for (const item of items) {
            if (item.saved_data && item.saved_data.length > 0) return item.saved_data[0];
            if (item.komponens) {
                const found = findFirstEntry(Object.values(item.komponens));
                if (found) return found;
            }
            if (item.sub_komponens) {
                const found = findFirstEntry(Object.values(item.sub_komponens));
                if (found) return found;
            }
            if (item.sub_komponens_2) {
                const found = findFirstEntry(Object.values(item.sub_komponens_2));
                if (found) return found;
            }
        }
        return null;
    };

    const firstEntry = findFirstEntry(data);
    const hasData = firstEntry !== null;
    const status = firstEntry ? firstEntry.status_anggaran : 'DRAFT';
    state.currentStatus = status;

    // Tampilkan Action Bar jika ada data
    elements.actionBar.style.display = hasData ? 'block' : 'none';
    elements.statusDisplay.textContent = `Status: ${status.replace(/_/g, ' ')}`;
    elements.statusDisplay.dataset.status = status;


    const isEditable = (status === 'DRAFT' || status === 'DITOLAK' || status === 'REVISI_DRAFT');
    const isLocked = (status === 'MENUNGGU_VERIFIKASI');
    if (elements.revisiAnggaranBtn) {
    elements.revisiAnggaranBtn.style.display = (status === 'DISETUJUI') ? 'inline-block' : 'none';
}

    // Tampilkan tombol "Submit Anggaran" hanya jika bisa diedit
    elements.submitAnggaranBtn.style.display = isEditable && hasData ? 'block' : 'none';

    // Nonaktifkan tombol-tombol edit standar jika statusnya bukan DRAFT atau DITOLAK
    elements.rabContainer.querySelectorAll('.tambah-akun-btn, .tambah-detail-btn, .edit-detail-btn').forEach(btn => {
        btn.disabled = !isEditable;
        btn.style.opacity = isEditable ? '1' : '0.6';
        btn.style.cursor = isEditable ? 'pointer' : 'not-allowed';
    });
    
    // Atur atribut 'isLocked' HANYA jika statusnya benar-benar terkunci total
    elements.rabContainer.dataset.isLocked = isLocked;
};

// Di file: proyek_rab/js/dom.js
const renderSavedDetailsTree = (items, parentPath, entryContext, isReadOnly = false) => {
    let listHtml = '';
    items.forEach(item => {
        state.detailDataCache[item.id_rab_detail] = item;
        const isHeader = item.children && item.children.length > 0;
        
        // KUNCI PERBAIKAN: Cek apakah item ini BARU (total_biaya UTAMA adalah NULL)
        const isNewItemInRevision = item.total_biaya === null;

        // Data Utama (kosongkan jika item baru)
        const volumesData = JSON.parse(item.volume_data || '[]');
        let volumeHtml = isNewItemInRevision ? '' : volumesData.map(v => `<div>${v.volume} ${v.satuan}</div>`).join('');
        const hargaText = isNewItemInRevision ? '' : formatRupiah(item.harga_satuan);
        const totalText = isNewItemInRevision ? '' : formatRupiah(item.total_biaya);
        
        // Data Revisi (selalu tampilkan jika ada datanya)
        const volumesRevisiData = JSON.parse(item.volume_data_revisi || '[]');
        let volumeRevisiHtml = volumesRevisiData.map(v => `<div>${v.volume} ${v.satuan}</div>`).join('');
        const hargaRevisiText = formatRupiah(item.harga_satuan_revisi);
        const totalRevisiText = formatRupiah(item.total_biaya_revisi);

        const statusAnggaran = entryContext.status_anggaran || 'DRAFT';
        let actionButton = '';
        if (!isReadOnly && (statusAnggaran === 'DRAFT' || statusAnggaran === 'DITOLAK' || statusAnggaran === 'REVISI_DRAFT')) {
            if (!item.parent_detail_id) {
                actionButton = `<button class="edit-detail-btn btn-secondary btn-inline" data-id-detail="${item.id_rab_detail}">Edit</button>`;
            }
        }
        
        const toggleButton = isHeader ? `<button class="toggle-btn" data-path="${parentPath}.${item.id_rab_detail}">+</button>` : '';
        const detailContext = { ...entryContext, id_rab_detail: item.id_rab_detail, rab_entry_id: item.rab_entry_id };

        listHtml += `
            <div class="rab-table-row level-detail ${isHeader ? 'collapsible' : ''}" style="display: none;" data-path="${parentPath}.${item.id_rab_detail}" data-parent-path="${parentPath}" data-level="level-detail" data-context='${JSON.stringify(detailContext)}'>
                <div class="col-aksi">${toggleButton} ${actionButton}</div>
                <div class="col-mak"></div>
                <div class="col-uraian">${item.uraian_pekerjaan}</div>
                <div class="col-volume">${volumeHtml}</div>
                <div class="col-harga-satuan">${hargaText}</div>
                <div class="col-jumlah">${totalText}</div>
                <div class="col-volume col-revisi">${volumeRevisiHtml}</div>
                <div class="col-harga-satuan col-revisi">${hargaRevisiText}</div>
                <div class="col-jumlah col-revisi">${totalRevisiText}</div>
                <div class="col-catatan-revisi">${item.catatan_revisi || ''}</div>
                <div class="col-status-container">${item.status_revisi !== 'ORIGINAL' ? `<span class="col-status status-revisi">${item.status_revisi}</span>` : ''}</div>
            </div>`;

        if (isHeader) {
            listHtml += renderSavedDetailsTree(item.children, `${parentPath}.${item.id_rab_detail}`, entryContext, isReadOnly);
        }
    });
    return listHtml;
};

// Di file: proyek_rab/js/dom.js

const createRowHtml = (level, path, name, itemData, parentContext, isReadOnly = false) => {
    const isCollapsible = (itemData.komponens?.length > 0 || itemData.sub_komponens?.length > 0 || itemData.sub_komponens_2?.length > 0 || itemData.saved_data?.length > 0);
    const currentContext = { ...parentContext, ...itemData };
    delete currentContext.komponens;
    delete currentContext.sub_komponens;
    delete currentContext.sub_komponens_2;
    delete currentContext.saved_data;

    const idDataAttr = `data-id-${level.replace('level-', '')}="${itemData[`${level.replace('level-','_')}_id`] || ''}"`;
    
    // --- PERBAIKAN TAMPILAN TOTAL ---
    const totalAnggaranFormatted = formatRupiah(itemData.total_anggaran);
    // Tampilkan total revisi jika ada dan berbeda dari total utama
    const totalAnggaranRevisiFormatted = formatRupiah(itemData.total_anggaran_revisi); 
    
  let savedDataHtml = '';
    if (itemData.saved_data && itemData.saved_data.length > 0) {
        itemData.saved_data.forEach(entry => {
            const entryPath = path + '.' + entry.kode_akun;
            const entryContext = { ...currentContext, id_akun: entry.id_akun, kode_akun: entry.kode_akun, id_rab_entry: entry.id_rab_entry, status_anggaran: entry.status_anggaran };
            
            // Hitung total untuk baris Akun
            const totalAnggaranAkunFormatted = formatRupiah(entry.total_anggaran);
            const totalAnggaranAkunRevisiFormatted = formatRupiah(entry.total_anggaran_revisi);

            const hasDetails = entry.details && entry.details.length > 0;
            const toggleButton = hasDetails ? `<button class="toggle-btn" data-path="${entryPath}">+</button>` : '';
            const tambahDetailButton = !isReadOnly ? `<button class="tambah-detail-btn btn-secondary btn-inline" data-entry-id="${entry.id_rab_entry}">+ Detail</button>` : '';

            savedDataHtml += `
                <div class="rab-table-row level-akun ${hasDetails ? 'collapsible' : ''}" style="display: none;" data-path="${entryPath}" data-parent-path="${path}" data-level="level-akun" data-id-entry="${entry.id_rab_entry}" data-context='${JSON.stringify(entryContext)}'>
                    <div class="col-aksi">${toggleButton} ${tambahDetailButton}</div>
                    <div class="col-mak">${entry.kode_akun}</div>
                    <div class="col-uraian">${entry.uraian_akun}</div>
                    <div></div><div></div>
                    <div class="col-jumlah">${totalAnggaranAkunFormatted}</div>
                    <div></div><div></div>
                    <div class="col-jumlah col-revisi">${totalAnggaranAkunRevisiFormatted}</div>
                    <div></div><div></div>
                </div>`;
            if (hasDetails) {
                savedDataHtml += renderSavedDetailsTree(entry.details, entryPath, entryContext, isReadOnly);
            }
        });
    }

    let actionButton = '';
    if (!isReadOnly && (level === 'level-sub' || level === 'level-sub2' || level === 'level-komponen')) {
        actionButton = `<button class="tambah-akun-btn btn-primary btn-inline" data-context='${JSON.stringify(currentContext)}'>+ Akun</button>`;
    }

    const toggleButton = isCollapsible ? `<button class="toggle-btn" data-path="${path}">+</button>` : '';
    const dataPath = `data-path="${path}"`;
    const dataParentPath = parentContext.path ? `data-parent-path="${parentContext.path}"` : '';
    const displayStyle = (level === 'level-kro') ? '' : 'style="display: none;"';

    return `
        <div class="rab-table-row ${level} ${isCollapsible ? 'collapsible' : ''}" ${displayStyle} ${dataPath} ${dataParentPath} data-level="${level}" ${idDataAttr} data-context='${JSON.stringify(currentContext)}'>
            <div class="col-aksi">${toggleButton} ${actionButton}</div>
            <div class="col-mak">${path.split('.').pop()}</div>
            <div class="col-uraian">${name}</div>
            <div></div><div></div>
            <div class="col-jumlah">${totalAnggaranFormatted}</div>
            <div></div><div></div>
            <div class="col-jumlah col-revisi">${totalAnggaranRevisiFormatted}</div>
            <div></div><div></div>
        </div>` + savedDataHtml;
};

export const loadHierarchyTable = (targetId, kegiatanId, kroId, bagianId, callback, isReadOnly = false) => {
    const targetElement = document.getElementById(targetId);
    if (!targetElement) {
        console.error(`Elemen dengan ID '${targetId}' tidak ditemukan.`);
        return;
    }
    targetElement.innerHTML = `<p>Memuat...</p>`;
    
    if (targetId === 'rab-container') {
        state.detailDataCache = {};
    }

    api.fetchHierarchyTable(kegiatanId, kroId, bagianId)
        .then(data => {
            if (data.error || (Array.isArray(data) && data.length === 0)) {
                targetElement.innerHTML = `<p>Tidak ada data anggaran yang ditemukan.</p>`;
                if (targetId === 'rab-container' && elements.actionBar) {
                    elements.actionBar.style.display = 'none';
                }
                return;
            }
            let kroUraian = "Data Anggaran";
            if (targetId === 'rab-container' && elements.kroTerpilihDisplay) {
                kroUraian = elements.kroTerpilihDisplay.textContent.split(' - ')[1] || 'Uraian KRO';
            } else if (state.currentPengajuan) {
                kroUraian = state.currentPengajuan.nama_kro;
            }

            renderHierarchyTable(targetId, data, kroUraian, kroId, isReadOnly); // <-- Teruskan parameter isReadOnly
            
            if (typeof callback === 'function') {
                callback();
            }
        });
};

// Di file: proyek_rab/js/dom.js

export const renderHierarchyTable = (targetId, data, kroUraian, kroId, isReadOnly = false) => {
    const targetElement = document.getElementById(targetId);
    if (!targetElement) return;

    let tableBodyHtml = '';
    const kroMak = kroId;
    
    let kroTotal = 0;
    let kroTotalRevisi = 0;
    
    const safeArray = (value) => (value && typeof value === 'object' && !Array.isArray(value)) ? Object.values(value) : (value || []);

    safeArray(data).forEach(ro => {
        kroTotal += (parseFloat(ro.total_anggaran) || 0);
        
        // --- PERBAIKAN LOGIKA KALKULASI DI SINI ---
        kroTotalRevisi += (parseFloat(ro.total_anggaran_revisi) || parseFloat(ro.total_anggaran) || 0);
    });

    tableBodyHtml += createRowHtml('level-kro', kroMak, kroUraian, {
        total_anggaran: kroTotal,
        total_anggaran_revisi: kroTotalRevisi,
        komponens: data,
        kro_id: kroMak
    }, {}, isReadOnly);
    
    safeArray(data).forEach(ro => {
        const roPath = `${kroMak}.${ro.no_ro}`;
        tableBodyHtml += createRowHtml('level-ro', roPath, ro.nama_ro, ro, { path: kroMak, ro_id: ro.ro_id }, isReadOnly);
        safeArray(ro.komponens).forEach(komp => {
            const kompPath = `${roPath}.${komp.kode_komponen}`;
            tableBodyHtml += createRowHtml('level-komponen', kompPath, komp.nama_komponen, komp, { path: roPath, ro_id: ro.ro_id, komponen_id: komp.komponen_id }, isReadOnly);
            safeArray(komp.sub_komponens).forEach(sub => {
                const subPath = `${kompPath}.${sub.kode_sub_komponen}`;
                tableBodyHtml += createRowHtml('level-sub', subPath, sub.nama_sub_komponen, sub, { path: kompPath, ro_id: ro.ro_id, komponen_id: komp.komponen_id, sub_komponen_id: sub.sub_komponen_id }, isReadOnly);
                safeArray(sub.sub_komponens_2).forEach(sub2 => {
                    const sub2Path = `${subPath}.${sub2.kode_sub_komponen_2}`;
                    tableBodyHtml += createRowHtml('level-sub2', sub2Path, sub2.nama_sub_komponen_2, sub2, { path: subPath, ro_id: ro.ro_id, komponen_id: komp.komponen_id, sub_komponen_id: sub.sub_komponen_id, sub_komponen_2_id: sub2.sub_komponen_2_id }, isReadOnly);
                });
            });
        });
    });

    targetElement.innerHTML = `
        <div class="rab-table">
            <div class="rab-table-header">
                <div class="col-aksi">Aksi</div> <div class="col-mak">MAK</div> <div class="col-uraian">Uraian</div>
                <div class="col-volume">Volume</div> <div>Harga Satuan</div> <div class="col-jumlah">Jumlah</div>
                <div class="col-volume">Volume (Rev)</div> <div>Harga (Rev)</div> <div class="col-jumlah">Jumlah (Rev)</div>
                <div>Catatan Revisi</div> <div>Status</div>
            </div>
            <div class="rab-table-body">${tableBodyHtml}</div>
        </div>`;
        
    const kroRow = targetElement.querySelector(`.rab-table-row[data-path="${kroMak}"]`);
    if(kroRow) {
        kroRow.classList.add('expanded');
        const toggle = kroRow.querySelector('.toggle-btn');
        if(toggle) toggle.textContent = '−';
    }
    targetElement.querySelectorAll(`.rab-table-row[data-parent-path="${kroMak}"]`).forEach(row => {
        row.style.display = 'grid';
    });

    if (targetId === 'rab-container') {
        if (elements.bagianFilterContainer && data.length > 0) elements.bagianFilterContainer.style.display = 'block';
        if (elements.searchContainer && data.length > 0) elements.searchContainer.style.display = 'block';
        updateUiByStatus(data);
    }
};

export const toggleCollapse = (button) => {
    const row = button.closest('.rab-table-row');
    if (!row) return;
    const path = row.dataset.path;
    const isExpanded = row.classList.toggle('expanded');
    button.textContent = isExpanded ? '−' : '+';
    const children = document.querySelectorAll(`[data-parent-path="${path}"]`);
    children.forEach(child => {
        child.style.display = isExpanded ? 'grid' : 'none';
        if (!isExpanded) {
            child.classList.remove('expanded');
            const childToggleButton = child.querySelector('.toggle-btn');
            if (childToggleButton) childToggleButton.textContent = '+';
            const descendants = document.querySelectorAll(`[data-parent-path^="${child.dataset.path}"]`);
            descendants.forEach(desc => desc.style.display = 'none');
        }
    });
};


export const showDirectDetailForm = () => {
    const formContainer = document.getElementById('form-container-root');
    formContainer.innerHTML = `<button type="button" id="back-to-choice-btn" class="btn-secondary" style="margin-bottom: 15px;">&laquo; Kembali ke Pilihan</button>`;
    const entryId = document.getElementById('simpan-semua-btn').dataset.entryId;
    const formShell = createFormGridShell('input-detail-form', entryId);
    formContainer.insertAdjacentHTML('beforeend', formShell);
    formContainer.querySelector('#detail-rows-container').innerHTML = createDetailInputRowHTML();
    calculateTotalBiaya(formContainer.querySelector('form'));
};

export const showHeaderForm = () => {
    const formContainer = document.getElementById('form-container-root');
    formContainer.innerHTML = `
        <button type="button" id="back-to-choice-btn" class="btn-secondary" style="margin-bottom: 15px;">&laquo; Kembali ke Pilihan</button>
        <div id="header-input-stage">
            <div class="form-header-group">
                <label for="input-header-uraian">Tahap 1: Masukkan Uraian Header/Grup</label>
                <input type="text" id="input-header-uraian" placeholder="Contoh: Panitia, Perjalanan Dinas, dll.">
            </div>
            <button type="button" id="lanjutkan-ke-rincian-btn" class="btn-primary">Lanjutkan & Tambah Rincian</button>
        </div>
        <div id="detail-grid-stage" style="display: none;"></div>
    `;
};

export const showDetailGridForHeader = () => {
    const headerInputStage = document.getElementById('header-input-stage');
    const detailGridStage = document.getElementById('detail-grid-stage');
    const headerUraianInput = document.getElementById('input-header-uraian');
    headerInputStage.style.display = 'none';
    const entryId = document.getElementById('simpan-semua-btn').dataset.entryId;
    const formShell = createFormGridShell('input-detail-form', entryId);
    detailGridStage.innerHTML = formShell;
    detailGridStage.style.display = 'block';
    const formHeaderContainer = detailGridStage.querySelector('#form-header-container');
    formHeaderContainer.innerHTML = `
        <div class="form-header-group">
            <label>Header/Grup</label>
            <input type="text" id="input-header-uraian" readonly value="${headerUraianInput.value}">
        </div>
    `;
    detailGridStage.querySelector('#detail-rows-container').innerHTML = createDetailInputRowHTML();
    calculateTotalBiaya(detailGridStage.querySelector('form'));
};

export const openDetailModal = (entryId) => {
    const modalPath = document.getElementById('modal-path');
    const modalBody = document.getElementById('modal-body');
    modalPath.textContent = `Input Detail Untuk Entry ID: ${entryId}`;
    modalBody.innerHTML = `
        <div class="input-choice-container">
            <div class="input-choice" data-choice="header">
                <label><input type="radio" name="input_type" value="header"> Rekam Header + Rincian</label>
                <p>Untuk membuat grup yang akan memiliki beberapa rincian di bawahnya.</p>
            </div>
            <div class="input-choice" data-choice="detail">
                <label><input type="radio" name="input_type" value="detail"> Rekam Rincian Langsung</label>
                <p>Untuk input rincian tunggal tanpa grup/header.</p>
            </div>
        </div>
        <div id="form-container-root"></div>
        <button id="simpan-semua-btn" data-entry-id="${entryId}" style="display:none;"></button>
    `;
    elements.modal.classList.add('visible');
};

export const formatNumberInput = (input) => {
    let value = input.value.replace(/[^0-9]/g, '');
    if (value === '') {
        input.value = '';
        return;
    }
    const numberValue = parseInt(value, 10);
    input.value = new Intl.NumberFormat('id-ID').format(numberValue);
};

export const calculateTotalBiaya = (form) => {
    if (!form) return;
    let totalBiaya = 0;
    form.querySelectorAll('.detail-form-grid-row').forEach(row => {
        let totalVolume = 1;
        let hasVolume = false;
        row.querySelectorAll('.volume-input').forEach(input => {
            const value = parseFloat(input.value) || 0;
            if (value > 0) {
                totalVolume *= value;
                hasVolume = true;
            }
        });
        if (!hasVolume) {
            totalVolume = 0;
        }
        const hargaSatuanInput = row.querySelector('.harga-input');
        const hargaSatuan = parseFloat(hargaSatuanInput.value.replace(/\./g, '')) || 0;
        totalBiaya += totalVolume * hargaSatuan;
    });
    const displayElement = form.querySelector('#total-biaya-otomatis');
    if (displayElement) {
        displayElement.textContent = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(totalBiaya);
    }
};

// Di dalam file: proyek_rab/js/dom.js

export const openEditModal = (detailId, entryId, mode = 'edit') => {
    const detailToEdit = state.detailDataCache[detailId];
    if (!detailToEdit) {
        showToast('error', 'Data detail tidak ditemukan.');
        return;
    }

    const idDetailAsli = detailToEdit.parent_detail_id || detailId;
    const isRevision = (mode === 'revisi'); // Tentukan apakah ini mode revisi

    const editModalBody = elements.editModalBody;
    const formShell = createFormGridShell('edit-detail-form', entryId, false);
    editModalBody.innerHTML = formShell;

    const form = editModalBody.querySelector('form');
    form.dataset.mode = mode;
    form.dataset.idDetailAsli = idDetailAsli;

    // --- PERBAIKAN DI SINI: Teruskan flag 'isRevision' ---
    const detailRowHTML = createDetailInputRowHTML(detailToEdit, false, isRevision);
    
    const rowsContainer = editModalBody.querySelector('#detail-rows-container');
    rowsContainer.innerHTML = detailRowHTML;

    const formHeaderContainer = editModalBody.querySelector('#form-header-container');
    if (formHeaderContainer) {
        formHeaderContainer.remove();
    }
    
    const modalTitle = elements.editModal.querySelector('#edit-modal-title');
    const updateBtn = document.getElementById('update-detail-btn');
    const catatanContainer = document.createElement('div');
    catatanContainer.id = 'catatan-revisi-container';
    form.insertBefore(catatanContainer, form.querySelector('.modal-form-footer'));

    if (isRevision) {
        modalTitle.textContent = 'Formulir Pengajuan Revisi';
        updateBtn.textContent = 'Ajukan Revisi';
        catatanContainer.innerHTML = `
            <div class="form-header-group" style="margin-top: 15px;">
                <label for="input-catatan-revisi">Catatan / Alasan Revisi (Wajib Diisi)</label>
                <textarea id="input-catatan-revisi" placeholder="Jelaskan alasan pengajuan revisi..." rows="3" style="width:100%; padding:8px; border-radius: 4px; border: 1px solid #ccc;">${detailToEdit.catatan_revisi || ''}</textarea>
            </div>
        `;
    } else {
        modalTitle.textContent = 'Edit Detail Anggaran';
        updateBtn.textContent = 'Simpan Perubahan';
    }

    elements.editModal.classList.add('visible');
    calculateTotalBiaya(form);
};

export const openSelectionModal = (title, fetchPromise, onSelectCallback) => {
    elements.selectionModalTitle.textContent = title;
    elements.selectionModalBody.innerHTML = '<p>Memuat...</p>';
    elements.selectionModal.classList.add('visible');
    fetchPromise.then(data => {
        let html = '<ul>';
        if (data && data.length > 0) {
            data.forEach(item => {
                const id = item.kegiatan_id || item.kro_id;
                const nama = item.nama_kegiatan || item.nama_kro;
                const programId = item.Program_id || '';
                html += `<li data-id="${id}" data-nama="${nama}" data-programid="${programId}"><strong>${id}</strong> - ${nama}</li>`;
            });
        } else {
            html += '<li>Tidak ada data.</li>';
        }
        html += '</ul>';
        elements.selectionModalBody.innerHTML = html;
        elements.selectionModalBody.querySelectorAll('li').forEach(item => {
            item.addEventListener('click', function (e) {
                if (this.dataset.id) {
                    onSelectCallback({
                        id: this.dataset.id,
                        nama: this.dataset.nama,
                        programid: this.dataset.programid
                    });
                    elements.selectionModal.classList.remove('visible');
                }
            });
        });
    }).catch(error => {
        console.error("Fetch Error:", error)
    });
};

export const createAkunEditForm = (selectedId, currentKodeAkun) => {
    return api.fetchKodeAkun().then(data => {
        let html = `<div class="form-group-akun"><label>Pilih Kode Akun:</label><select id="edit-kode-akun-select" data-id-rab-entry="${selectedId}"><option value="">-- Pilih --</option>`;
        data.forEach(akun => {
            const isSelected = (akun.kode_akun === currentKodeAkun) ? 'selected' : '';
            html += `<option value="${akun.id_akun}" data-kodeakun="${akun.kode_akun}" ${isSelected}>${akun.kode_akun} - ${akun.uraian_akun}</option>`;
        });
        html += `</select></div>`;
        return html;
    });
};

export const openAkunModal = (context) => {
    const modalPath = document.getElementById('modal-path');
    const modalBody = document.getElementById('modal-body');
    modalPath.textContent = `Rekam Akun Baru`;
    modalBody.innerHTML = '<p>Memuat...</p>';
    api.fetchKodeAkun().then(data => {
        let html = `<div class="form-group-akun"><label>Pilih Kode Akun:</label><select id="kode-akun-select"><option value="">-- Pilih --</option>`;
        data.forEach(akun => {
            html += `<option value="${akun.id_akun}" data-kodeakun="${akun.kode_akun}">${akun.kode_akun} - ${akun.uraian_akun}</option>`;
        });
        html += `</select></div><button id="btn-rekam-akun" class="btn-primary" data-context='${JSON.stringify(context)}'>Rekam Akun</button>`;
        modalBody.innerHTML = html;
        elements.modal.classList.add('visible');
    });
};

export const openAddDetailModal = (entryId) => {
    elements.modal.classList.add('visible');
    const modalPath = document.getElementById('modal-path');
    const modalBody = document.getElementById('modal-body');
    modalPath.textContent = `Input Detail Baru Untuk Akun (ID: ${entryId})`;
    modalBody.innerHTML = createFormGridShell('add-new-detail-form', entryId, true);
    const rowsContainer = modalBody.querySelector('#detail-rows-container');
    rowsContainer.innerHTML = createDetailInputRowHTML();
};

export const openAkunEditModal = (idRabEntry, kodeAkun) => {
    const title = elements.editAkunModal.querySelector('#edit-akun-modal-title');
    const path = elements.editAkunModal.querySelector('#edit-akun-modal-path');
    title.textContent = 'Edit Kode Akun';
    path.textContent = `Mengedit RAB Entry: ${kodeAkun} (ID: ${idRabEntry})`;
    createAkunEditForm(idRabEntry, kodeAkun).then(formHtml => {
        elements.editAkunFormContainer.innerHTML = formHtml;
        elements.editAkunModal.classList.add('visible');
    });
};

export const closeAkunModal = () => {
    elements.modal.classList.remove('visible');
};
export const closeEditModal = () => {
    elements.editModal.classList.remove('visible');
};
export const closeAkunEditModal = () => {
    elements.editAkunModal.classList.remove('visible');
};

export const showContextMenu = (x, y, level, context) => {
    const menu = elements.contextMenu;
    const items = {
        tambahAkun: menu.querySelector('[data-action="tambah-akun"]'),
        tambahDetail: menu.querySelector('[data-action="tambah-detail"]'),
        edit: menu.querySelector('[data-action="edit"]'),
        hapus: menu.querySelector('[data-action="hapus"]'),
        cetak: menu.querySelector('[data-action="cetak"]'),
        separators: menu.querySelectorAll('.separator')
    };
    Object.values(items).forEach(itemOrList => {
        if (NodeList.prototype.isPrototypeOf(itemOrList)) {
            itemOrList.forEach(el => {
                el.style.display = 'none';
            });
        } else {
            itemOrList.style.display = 'none';
        }
    });
    switch (level) {
        case 'level-kro':
        case 'level-ro':
        case 'level-komponen':
            items.edit.style.display = 'list-item';
            items.hapus.style.display = 'list-item';
            break;
        case 'level-sub':
        case 'level-sub2':
            items.tambahAkun.style.display = 'list-item';
            items.edit.style.display = 'list-item';
            items.hapus.style.display = 'list-item';
            break;
        case 'level-akun':
            items.tambahDetail.style.display = 'list-item';
            items.edit.style.display = 'list-item';
            items.hapus.style.display = 'list-item';
            break;
        case 'level-detail':
            items.edit.textContent = 'Edit Detail';
            items.edit.style.display = 'list-item';
            items.hapus.style.display = 'list-item';
            break;
    }
    if (level !== 'level-detail') {
        items.edit.textContent = 'Edit';
    }
    menu.style.display = 'block';
    menu.style.left = `${x}px`;
    menu.style.top = `${y}px`;
};

export const hideContextMenu = () => {
    if (elements.contextMenu) {
        elements.contextMenu.style.display = 'none';
    }
};