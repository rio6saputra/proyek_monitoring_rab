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
        submitAnggaranBtn: document.getElementById('submit-anggaran-btn')
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
                <div class="detail-form-grid" style="grid-template-columns: minmax(250px, 2.5fr) repeat(4, minmax(100px, 1fr)) 1.5fr 60px;">
                    <div class="detail-form-grid-header">Uraian</div>
                    <div class="detail-form-grid-header">Volume / Satuan 1</div>
                    <div class="detail-form-grid-header">Volume / Satuan 2</div>
                    <div class="detail-form-grid-header">Volume / Satuan 3</div>
                    <div class="detail-form-grid-header">Volume / Satuan 4</div>
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

export const createDetailInputRowHTML = (detail = {}) => {
    let volumePairs = '';
    for (let i = 0; i < 4; i++) {
        const vol = detail.volumes?.[i]?.volume || '';
        const sat = detail.volumes?.[i]?.satuan || '';
        volumePairs += `
            <div class="vol-sat-pair">
                <input type="number" class="volume-input" placeholder="Volume ${i+1}" value="${vol}">
                <input type="text" class="satuan-input" placeholder="Satuan ${i+1}" value="${sat}">
            </div>
        `;
    }
    const formattedHarga = detail.harga_satuan ? new Intl.NumberFormat('id-ID').format(detail.harga_satuan) : '';
    return `
        <div class="detail-form-grid-row" data-id-detail="${detail.id_rab_detail || ''}">
            <textarea class="uraian-input" placeholder="Uraian pekerjaan..." rows="1">${detail.uraian_pekerjaan || ''}</textarea>
            ${volumePairs}
            <input type="text" class="harga-input" placeholder="Harga Satuan" value="${formattedHarga}" inputmode="numeric">
            <button type="button" class="delete-row-btn" title="Hapus Baris">×</button>
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
    const status = firstEntry ? firstEntry.status_anggaran : 'DRAFT';
    const hasData = firstEntry !== null;

    elements.actionBar.style.display = hasData ? 'block' : 'none';
    elements.statusDisplay.textContent = `Status: ${status.replace(/_/g, ' ')}`;
    elements.statusDisplay.dataset.status = status;

    const isEditable = (status === 'DRAFT' || status === 'DITOLAK');

    elements.submitAnggaranBtn.style.display = isEditable && hasData ? 'block' : 'none';

    elements.rabContainer.querySelectorAll('.tambah-akun-btn, .tambah-detail-btn, .edit-detail-btn').forEach(btn => {
        btn.disabled = !isEditable;
        btn.style.opacity = isEditable ? '1' : '0.6';
        btn.style.cursor = isEditable ? 'pointer' : 'not-allowed';
    });
    
    elements.rabContainer.dataset.isLocked = !isEditable;
};

const renderSavedDetailsTree = (items, parentPath, entryContext) => {
    let listHtml = '';
    items.forEach(item => {
        state.detailDataCache[item.id_rab_detail] = item;
        const isHeader = item.children && item.children.length > 0;

        const isRevisi = item.status_revisi === 'REVISI';
        let volumeHtml = (item.volumes && item.volumes.length > 0) ? item.volumes.map(v => `<div>${v.volume} ${v.satuan}</div>`).join('') : '';
        const hargaText = formatRupiah(item.harga_satuan);
        const totalText = formatRupiah(item.total_biaya);
        const volumesRevisiData = isRevisi ? JSON.parse(item.volume_data_revisi || '[]') : [];
        let volumeRevisiHtml = isRevisi ? volumesRevisiData.map(v => `<div>${v.volume} ${v.satuan}</div>`).join('') : '';
        const hargaRevisiText = isRevisi ? formatRupiah(item.harga_satuan_revisi) : '';
        const totalRevisiText = isRevisi ? formatRupiah(item.total_biaya_revisi) : '';
        let statusClass = '',
            statusText = item.status_revisi.replace('_', ' ');
        if (item.status_revisi === 'REVISI') statusClass = 'status-revisi';
        else if (item.status_revisi === 'DISETUJUI') statusClass = 'status-disetujui';
        else if (item.status_revisi === 'DITOLAK') statusClass = 'status-ditolak';

        const toggleButton = isHeader ? `<button class="toggle-btn" data-path="${parentPath}.${item.id_rab_detail}">+</button>` : '';
        const editButton = item.parent_detail_id == null ? `<button class="edit-detail-btn btn-secondary btn-inline" data-id-detail="${item.id_rab_detail}">Edit</button>` : '';
        const detailContext = { ...entryContext,
            id_rab_detail: item.id_rab_detail,
            rab_entry_id: item.rab_entry_id
        };

        listHtml += `
            <div class="rab-table-row level-detail ${isHeader ? 'collapsible' : ''}" style="display: none;" data-path="${parentPath}.${item.id_rab_detail}" data-parent-path="${parentPath}" data-level="level-detail" data-context='${JSON.stringify(detailContext)}'>
                <div class="col-aksi">${toggleButton} ${editButton}</div>
                <div class="col-mak"></div>
                <div class="col-uraian">${item.uraian_pekerjaan}</div>
                <div class="col-volume">${volumeHtml}</div>
                <div class="col-harga-satuan">${hargaText}</div>
                <div class="col-jumlah">${totalText}</div>
                <div class="col-volume col-revisi">${volumeRevisiHtml}</div>
                <div class="col-harga-satuan col-revisi">${hargaRevisiText}</div>
                <div class="col-jumlah col-revisi">${totalRevisiText}</div>
                <div class="col-catatan-revisi">${item.catatan_revisi || ''}</div>
                <div class="col-status-container">
                    ${statusClass ? `<span class="col-status ${statusClass}" data-id-detail="${item.id_rab_detail}">${statusText}</span>` : ''}
                </div>
            </div>`;

        if (isHeader) {
            listHtml += renderSavedDetailsTree(item.children, `${parentPath}.${item.id_rab_detail}`, entryContext);
        }
    });
    return listHtml;
};

const createRowHtml = (level, path, name, itemData, parentContext) => {
    const isCollapsible = (itemData.komponens?.length > 0 || itemData.sub_komponens?.length > 0 || itemData.sub_komponens_2?.length > 0 || itemData.saved_data?.length > 0);
    const currentContext = { ...parentContext, ...itemData };
    delete currentContext.komponens;
    delete currentContext.sub_komponens;
    delete currentContext.sub_komponens_2;
    delete currentContext.saved_data;

    const idDataAttr = `data-id-${level.replace('level-', '')}="${itemData[`${level.replace('level-','_')}_id`] || ''}"`;
    const totalAnggaranFormatted = formatRupiah(itemData.total_anggaran);

    let savedDataHtml = '';
    if (itemData.saved_data && itemData.saved_data.length > 0) {
        itemData.saved_data.forEach(entry => {
            const entryPath = path + '.' + entry.kode_akun;
            const entryContext = { ...currentContext,
                id_akun: entry.id_akun,
                kode_akun: entry.kode_akun,
                id_rab_entry: entry.id_rab_entry
            };
            const totalAnggaranAkunFormatted = formatRupiah(entry.total_anggaran);
            const hasDetails = entry.details && entry.details.length > 0;
            const toggleButton = hasDetails ? `<button class="toggle-btn" data-path="${entryPath}">+</button>` : '';

            savedDataHtml += `
                <div class="rab-table-row level-akun ${hasDetails ? 'collapsible' : ''}" style="display: none;" data-path="${entryPath}" data-parent-path="${path}" data-level="level-akun" data-id-entry="${entry.id_rab_entry}" data-context='${JSON.stringify(entryContext)}'>
                    <div class="col-aksi">${toggleButton} <button class="tambah-detail-btn btn-secondary btn-inline" data-entry-id="${entry.id_rab_entry}">+ Detail</button></div>
                    <div class="col-mak">${entry.kode_akun}</div>
                    <div class="col-uraian">${entry.uraian_akun}</div>
                    <div></div><div></div>
                    <div class="col-jumlah">${totalAnggaranAkunFormatted}</div>
                    <div></div><div></div><div></div><div></div><div></div>
                </div>`;
            if (hasDetails) {
                savedDataHtml += renderSavedDetailsTree(entry.details, entryPath, entryContext);
            }
        });
    }

    let actionButton = '';
    if (level === 'level-sub' || level === 'level-sub2' || level === 'level-komponen') {
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
            <div></div><div></div><div></div><div></div><div></div>
        </div>` + savedDataHtml;
};

export const loadHierarchyTable = (targetId, kegiatanId, kroId, bagianId, callback) => {
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

            renderHierarchyTable(targetId, data, kroUraian, kroId);
            
            if (typeof callback === 'function') {
                callback();
            }
        });
};

export const renderHierarchyTable = (targetId, data, kroUraian, kroId) => {
    const targetElement = document.getElementById(targetId);
    if (!targetElement) return;

    let tableBodyHtml = '';
    const kroMak = kroId;
    
    let kroTotal = 0;
    data.forEach(ro => { kroTotal += (parseFloat(ro.total_anggaran) || 0); });
    
    tableBodyHtml += createRowHtml('level-kro', kroMak, kroUraian, { total_anggaran: kroTotal, komponens: data, kro_id: kroMak }, {});
    
    // [PERBAIKAN KUNCI] Fungsi ini memastikan data objek dari PHP bisa di-loop
    const safeArray = (value) => (value && typeof value === 'object' && !Array.isArray(value)) ? Object.values(value) : (value || []);

    safeArray(data).forEach(ro => {
        const roPath = `${kroMak}.${ro.no_ro}`;
        tableBodyHtml += createRowHtml('level-ro', roPath, ro.nama_ro, ro, { path: kroMak, ro_id: ro.ro_id });
        safeArray(ro.komponens).forEach(komp => {
            const kompPath = `${roPath}.${komp.kode_komponen}`;
            tableBodyHtml += createRowHtml('level-komponen', kompPath, komp.nama_komponen, komp, { path: roPath, ro_id: ro.ro_id, komponen_id: komp.komponen_id });
            safeArray(komp.sub_komponens).forEach(sub => {
                const subPath = `${kompPath}.${sub.kode_sub_komponen}`;
                tableBodyHtml += createRowHtml('level-sub', subPath, sub.nama_sub_komponen, sub, { path: kompPath, ro_id: ro.ro_id, komponen_id: komp.komponen_id, sub_komponen_id: sub.sub_komponen_id });
                safeArray(sub.sub_komponens_2).forEach(sub2 => {
                    const sub2Path = `${subPath}.${sub2.kode_sub_komponen_2}`;
                    tableBodyHtml += createRowHtml('level-sub2', sub2Path, sub2.nama_sub_komponen_2, sub2, { path: subPath, ro_id: ro.ro_id, komponen_id: komp.komponen_id, sub_komponen_id: sub.sub_komponen_id, sub_komponen_2_id: sub2.sub_komponen_2_id });
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
        
    // [PERBAIKAN KUNCI] Logika ini sekarang menargetkan elemen yang benar di halaman manapun
    const kroRow = targetElement.querySelector(`.rab-table-row[data-path="${kroMak}"]`);
    if(kroRow) {
        kroRow.classList.add('expanded');
        const toggle = kroRow.querySelector('.toggle-btn');
        if(toggle) toggle.textContent = '−';
    }
    targetElement.querySelectorAll(`.rab-table-row[data-parent-path="${kroMak}"]`).forEach(row => {
        row.style.display = 'grid';
    });

    // Jalankan logika UI/UX spesifik hanya jika ini adalah halaman input_rab
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

export const openEditModal = (detailId, entryId) => {
    const detailToEdit = state.detailDataCache[detailId];
    if (!detailToEdit) {
        dom.showToast('error', 'Data detail tidak ditemukan untuk diedit.');
        return;
    }
    const editModalBody = elements.editModalBody;
    const formShell = createFormGridShell('edit-detail-form', entryId, false);
    editModalBody.innerHTML = formShell;
    
    let allDetailRowsHTML = '';
    
    if (detailToEdit.parent_detail_id == null && detailToEdit.children) {
        const formHeaderContainer = editModalBody.querySelector('#form-header-container');
        formHeaderContainer.innerHTML = `
            <div class="form-header-group">
                <label>Header/Grup</label>
                <input type="text" id="input-header-uraian" value="${detailToEdit.uraian_pekerjaan}">
            </div>
        `;
        detailToEdit.children.forEach(child => {
            allDetailRowsHTML += createDetailInputRowHTML(child);
        });
    } else {
        allDetailRowsHTML += createDetailInputRowHTML(detailToEdit);
    }
    
    const rowsContainer = editModalBody.querySelector('#detail-rows-container');
    rowsContainer.innerHTML = allDetailRowsHTML;
    
    elements.editModal.classList.add('visible');
    calculateTotalBiaya(editModalBody.querySelector('form'));
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