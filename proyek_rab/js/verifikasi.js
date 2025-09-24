// js/verifikasi.js

import * as api from './api.js';
import * as dom from './dom.js';

// State khusus untuk halaman verifikasi
let state = {
    allPengajuan: [],
    currentPengajuan: null,
    elements: {}
};

/**
 * Menginisialisasi semua elemen DOM yang dibutuhkan di halaman ini.
 */
const initializeElements = () => {
    state.elements = {
        filterBagian: document.getElementById('filter-bagian-verifikasi'),
        daftarContainer: document.getElementById('daftar-pengajuan-container'),
        detailContainer: document.getElementById('detail-verifikasi-container'),
        detailTitle: document.getElementById('detail-verifikasi-title'),
        rabContainer: document.getElementById('rab-container-verifikasi'),
        btnSetujui: document.getElementById('btn-setujui'),
        btnTolak: document.getElementById('btn-tolak')
    };
};

/**
 * Merender daftar pengajuan ke dalam HTML.
 * @param {Array} pengajuanList - Daftar pengajuan yang akan ditampilkan.
 */
const renderDaftarPengajuan = (pengajuanList) => {
    const { daftarContainer } = state.elements;
    if (pengajuanList.length === 0) {
        daftarContainer.innerHTML = '<p>Tidak ada pengajuan yang menunggu verifikasi.</p>';
        return;
    }

    let html = '<ul class="selection-list">';
    pengajuanList.forEach(item => {
        // Tentukan label berdasarkan 'tipe_pengajuan' dari API
        const tipeLabel = item.tipe_pengajuan === 'Revisi' ? '<span class="status-revisi" style="font-size: 0.8em; padding: 2px 6px; border-radius: 4px; margin-left: 8px;">REVISI</span>' : '';

        html += `<li data-pengajuan='${JSON.stringify(item)}'>
                    <strong>${item.nama_bagian}</strong>${tipeLabel}<br>
                    <small>${item.nama_kegiatan} / ${item.nama_kro}</small>
                 </li>`;
    });
    html += '</ul>';
    daftarContainer.innerHTML = html;
};

/**
 * Mengisi dropdown filter dengan data bagian yang unik.
 */
const populateFilter = () => {
    const { filterBagian } = state.elements;
    const uniqueBagian = [...new Map(state.allPengajuan.map(item => [item.bagian_id, item])).values()];
    
    filterBagian.innerHTML = '<option value="all">-- Tampilkan Semua Pengajuan --</option>';
    uniqueBagian.forEach(item => {
        const option = document.createElement('option');
        option.value = item.bagian_id;
        option.textContent = item.nama_bagian;
        filterBagian.appendChild(option);
    });
};

/**
 * Memuat semua data pengajuan dari server.
 */
const loadPengajuan = () => {
    const { daftarContainer, detailContainer } = state.elements;
    daftarContainer.innerHTML = '<p>Memuat daftar pengajuan...</p>';
    detailContainer.style.display = 'none';

    api.fetchPengajuan().then(response => {
        if (response.status === 'success') {
            state.allPengajuan = response.data;
            renderDaftarPengajuan(state.allPengajuan);
            populateFilter();
        } else {
            daftarContainer.innerHTML = `<p style="color: red;">Gagal memuat data: ${response.message}</p>`;
        }
    });
};

/**
 * Menangani aksi verifikasi (setuju/tolak).
 * @param {string} action - Aksi yang dipilih ('setujui' atau 'tolak').
 */
const handleVerifikasiAction = (action) => {
    if (!state.currentPengajuan) return;

    const { kegiatan_id, kro_id, bagian_id } = state.currentPengajuan;
    const textAction = action === 'setujui' ? 'menyetujui' : 'menolak';

    Swal.fire({
        title: `Anda yakin ingin ${textAction} anggaran ini?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: `Ya, ${action}!`,
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            const payload = { kegiatan_id, kro_id, bagian_id, action };
            api.verifikasiAnggaran(payload).then(res => {
                if (res.status === 'success') {
                    dom.showToast('success', res.message);
                    loadPengajuan(); // Muat ulang daftar pengajuan
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                }
            });
        }
    });
};

/**
 * Mendaftarkan semua event listener untuk halaman ini.
 */
const initializeEventListeners = () => {
    const { daftarContainer, filterBagian, btnSetujui, btnTolak, rabContainer } = state.elements;

    // Listener untuk filter
    filterBagian.addEventListener('change', () => {
        const selectedBagianId = filterBagian.value;
        state.elements.detailContainer.style.display = 'none';
        state.currentPengajuan = null;

        if (selectedBagianId === 'all') {
            renderDaftarPengajuan(state.allPengajuan);
        } else {
            const filteredList = state.allPengajuan.filter(item => item.bagian_id == selectedBagianId);
            renderDaftarPengajuan(filteredList);
        }
    });

    // Listener untuk klik pada daftar pengajuan (event delegation)
    daftarContainer.addEventListener('click', (e) => {
        const listItem = e.target.closest('li');
        if (!listItem || !listItem.dataset.pengajuan) return;
        
        // Hapus highlight dari item lain dan tambahkan ke yang ini
        daftarContainer.querySelectorAll('li').forEach(li => li.classList.remove('active'));
        listItem.classList.add('active');

        const pengajuanData = JSON.parse(listItem.dataset.pengajuan);
        state.currentPengajuan = pengajuanData;
        
        const { detailContainer, detailTitle, rabContainer } = state.elements;
        detailTitle.textContent = `Detail Anggaran: ${pengajuanData.nama_bagian}`;
        rabContainer.innerHTML = '<p>Memuat detail anggaran...</p>';
        detailContainer.style.display = 'block';

        // Panggil fungsi dengan mode read-only diaktifkan
        dom.loadHierarchyTable('rab-container-verifikasi', pengajuanData.kegiatan_id, pengajuanData.kro_id, pengajuanData.bagian_id, null, true);
    });

        rabContainer.addEventListener('click', (e) => {
        const target = e.target;
        if (target.classList.contains('toggle-btn')) {
            // Panggil fungsi toggleCollapse dari dom.js
            dom.toggleCollapse(target);
        }
    });

    // Listener untuk tombol aksi
    btnSetujui.addEventListener('click', () => handleVerifikasiAction('setujui'));
    btnTolak.addEventListener('click', () => handleVerifikasiAction('tolak'));
};


// Titik masuk utama saat halaman dimuat
document.addEventListener('DOMContentLoaded', () => {
    initializeElements();
    initializeEventListeners();
    loadPengajuan(); // Langsung muat daftar pengajuan saat halaman dibuka
});