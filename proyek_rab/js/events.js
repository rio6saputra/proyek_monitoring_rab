// js/events.js (Versi Final dengan Implementasi Lengkap)

import state from './state.js';
import * as api from './api.js';
import * as dom from './dom.js';
import {
    elements
} from './dom.js';

// Di file: proyek_rab/js/events.js

const parseAndValidateDetailForm = (formElement) => {
    if (!formElement) return null;

    const allRows = formElement.querySelectorAll('.detail-form-grid-row');
    const details = [];
    const headerInput = formElement.querySelector('#input-header-uraian');

    allRows.forEach(row => {
        // 1. Kumpulkan data dari setiap baris seperti biasa
        const detailData = {
            id_rab_detail: row.dataset.idDetail || null,
            uraian: row.querySelector('.uraian-input').value.trim(),
            harga_satuan: row.querySelector('.harga-input').value.replace(/\./g, ''),
            volumes: [],
            children: [] // Siapkan properti 'children' untuk diisi
        };

        for (let i = 0; i < 5; i++) {
            const volumeInput = row.querySelectorAll('.volume-input')[i];
            const satuanInput = row.querySelectorAll('.satuan-input')[i];
            if (volumeInput?.value && satuanInput?.value) {
                detailData.volumes.push({
                    volume: volumeInput.value,
                    satuan: satuanInput.value
                });
            }
        }

        if (!detailData.uraian) return; // Lewati baris yang uraiannya kosong

        // 2. LOGIKA INTI: Cek apakah baris ini adalah sub-rincian
        if (row.classList.contains('sub-detail-row')) {
            // Jika YA, masukkan sebagai 'anak' dari rincian utama sebelumnya
            if (details.length > 0) {
                details[details.length - 1].children.push(detailData);
            }
        } else {
            // Jika BUKAN, masukkan sebagai rincian utama (induk)
            details.push(detailData);
        }
    });

    if (details.length === 0 && (!headerInput || !headerInput.value.trim())) {
        dom.showToast('error', 'Silakan isi setidaknya satu baris rincian detail.');
        return null;
    }

    // 3. Jika ada Header/Grup, bungkus semuanya
    if (headerInput && headerInput.value.trim()) {
        const headerId = formElement.dataset.idDetailAsli || null;
        return [{
            id_rab_detail: headerId,
            uraian: headerInput.value.trim(),
            harga_satuan: null,
            volumes: [],
            children: details
        }];
    }

    return details;
};

const toggleCollapse = (button) => {
    dom.toggleCollapse(button); // Sekarang kita panggil dari dom.js
};

const handleUpdateAkun = () => {
    const select = elements.editAkunFormContainer.querySelector('#edit-kode-akun-select');
    if (!select || !select.value) {
        dom.showToast('error', "Pilih kode akun.");
        return;
    }
    const selectedOption = select.options[select.selectedIndex];
    const payload = {
        id_rab_entry: select.dataset.idRabEntry,
        id_akun: select.value,
        kode_akun: selectedOption.dataset.kodeakun
    };
    api.editAkun(payload).then(result => {
        if (result.status === 'success') {
            dom.showToast('success', result.message);
            dom.closeAkunEditModal();
            const expandedPaths = dom.getExpandedPaths();
            dom.loadHierarchyTable('rab-container', state.selectedKegiatanId, state.selectedKroId, state.selectedBagianId, () => {
                dom.restoreExpandedState(expandedPaths);
            });
        } else {
            dom.showToast('error', result.message);
        }
    });
};

const handleGenericDelete = (level, context) => {
    let payload = {};
    let apiCall = null;
    let message = "";
    let id;

    switch (level) {
        case 'level-ro': id = context.ro_id; payload = { ro_id: id }; apiCall = api.hapusRo; message = "RO"; break;
        case 'level-komponen': id = context.komponen_id; payload = { komponen_id: id }; apiCall = api.hapusKomponen; message = "Komponen"; break;
        case 'level-sub': id = context.sub_komponen_id; payload = { sub_komponen_id: id }; apiCall = api.hapusSubKomponen; message = "Sub-Komponen"; break;
        case 'level-sub2': id = context.sub_komponen_2_id; payload = { sub_komponen_2_id: id }; apiCall = api.hapusSubKomponen2; message = "Sub-Komponen 2"; break;
        case 'level-akun': id = context.id_rab_entry; payload = { id_rab_entry: id }; apiCall = api.hapusAkun; message = "Akun"; break;
        case 'level-detail': id = context.id_rab_detail; payload = { id_rab_detail: id }; apiCall = api.hapusDetail; message = "Detail"; break;
        default: Swal.fire('Error', 'Tipe data untuk dihapus tidak valid.', 'error'); return;
    }

    if (!id) {
        Swal.fire('Error', `Gagal mendapatkan ID untuk ${message} yang akan dihapus.`, 'error');
        return;
    }

    Swal.fire({
        title: 'Apakah Anda Yakin?', text: `Data ${message} akan dihapus permanen!`, icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed && apiCall) {
            apiCall(payload).then(res => {
                if (res.status === 'success') {
                    Swal.fire('Berhasil!', res.message, 'success');
                    const expandedPaths = dom.getExpandedPaths();
                    dom.loadHierarchyTable('rab-container', state.selectedKegiatanId, state.selectedKroId, state.selectedBagianId, () => {
                        dom.restoreExpandedState(expandedPaths);
                    });
                } else {
                    Swal.fire('Gagal!', res.message || 'Terjadi kesalahan di server.', 'error');
                }
            }).catch(() => {
                Swal.fire('Error!', 'Gagal menghubungi server.', 'error');
            });
        }
    });
};

const handleContextMenu = (e) => {
    if (state.currentStatus !== 'DRAFT' && state.currentStatus !== 'DITOLAK' && state.currentStatus !== 'REVISI_DRAFT') {
        e.preventDefault(); // Mencegah menu muncul
        dom.showToast('info', 'Anggaran dalam status ini tidak dapat diubah.');
        return;
    }
    let row = e.target.closest('.rab-table-row');
    if (!row) { dom.hideContextMenu(); return; }
    e.preventDefault();
    const level = row.dataset.level;
    const context = JSON.parse(row.dataset.context || '{}');
    dom.showContextMenu(e.pageX, e.pageY, level, context);
    elements.contextMenu.dataset.context = JSON.stringify(context);
    elements.contextMenu.dataset.level = level;
};

const handleLiveSearch = (e) => {
    const searchTerm = e.target.value.toLowerCase().trim();
    const allRows = document.querySelectorAll('#rab-container .rab-table-row');
    if (!searchTerm) {
        allRows.forEach(row => { row.classList.remove('is-hidden-by-search'); });
        return;
    }
    const matchedRows = new Set();
    allRows.forEach(row => {
        const uraianCell = row.querySelector('.col-uraian');
        if (uraianCell && uraianCell.textContent.toLowerCase().includes(searchTerm)) {
            matchedRows.add(row);
            let current = row;
            while (current) {
                const parentPath = current.dataset.parentPath;
                if (!parentPath) break;
                const parentRow = document.querySelector(`.rab-table-row[data-path="${parentPath}"]`);
                if (parentRow) {
                    matchedRows.add(parentRow);
                    current = parentRow;
                } else { break; }
            }
        }
    });
    allRows.forEach(row => {
        if (matchedRows.has(row)) {
            row.classList.remove('is-hidden-by-search');
        } else {
            row.classList.add('is-hidden-by-search');
        }
    });
};

function openRevisiModalForDetail(detailId) {
    const detail = state.detailDataCache[detailId];
    if (!detail) {
        dom.showToast('error', 'Data detail tidak ditemukan.');
        return;
    }
    Swal.fire({
        title: 'Formulir Pengajuan Revisi',
        html: `
            <form id="form-revisi" style="text-align: left; padding: 10px;">
                <p><strong>Uraian:</strong><br>${detail.uraian_pekerjaan}</p>
                <hr style="margin: 1rem 0;">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="swal-harga-revisi" style="display: block; margin-bottom: 5px; font-weight: 500;">Harga Satuan Revisi</label>
                    <input id="swal-harga-revisi" class="swal2-input" style="width: 100%;" value="${detail.harga_satuan || ''}">
                </div>
                <div class="form-group">
                    <label for="swal-catatan-revisi" style="display: block; margin-bottom: 5px; font-weight: 500;">Catatan/Alasan Revisi (Wajib Diisi)</label>
                    <textarea id="swal-catatan-revisi" class="swal2-textarea" style="width: 100%;" placeholder="Jelaskan alasan pengajuan revisi..."></textarea>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Ajukan Revisi',
        cancelButtonText: 'Batal',
        focusConfirm: false,
        preConfirm: () => {
            const hargaRevisi = document.getElementById('swal-harga-revisi').value;
            const catatanRevisi = document.getElementById('swal-catatan-revisi').value;
            if (!catatanRevisi.trim()) {
                Swal.showValidationMessage('Catatan revisi tidak boleh kosong.');
                return false;
            }
            return {
                id_rab_detail: detailId,
                uraian: detail.uraian_pekerjaan,
                harga_satuan_revisi: parseFloat(hargaRevisi.replace(/\./g, '')) || 0,
                volumes_revisi: detail.volumes,
                catatan_revisi: catatanRevisi,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const payload = result.value;
            api.ajukanRevisi(payload).then(response => {
                if (response.status === 'success') {
                    dom.showToast('success', response.message);
                    dom.loadHierarchyTable('rab-container', state.selectedKegiatanId, state.selectedKroId, state.selectedBagianId);
                } else {
                    dom.showToast('error', response.message || 'Gagal mengajukan revisi.');
                }
            }).catch(() => dom.showToast('error', 'Terjadi kesalahan saat menghubungi server.'));
        }
    });
}

function openVerificationModalForAdmin(detailId) {
    const detail = state.detailDataCache[detailId];
    if (!detail) {
        dom.showToast('error', 'Data detail tidak ditemukan.');
        return;
    }
    const oldTotal = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(detail.total_biaya || 0);
    const newTotal = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(detail.total_biaya_revisi || 0);
    Swal.fire({
        title: 'Verifikasi Revisi Anggaran',
        html: `
            <div style="text-align: left; padding: 10px;">
                <p style="margin-bottom: 5px;"><strong>Uraian:</strong> ${detail.uraian_pekerjaan}</p>
                <p style="margin-bottom: 5px;"><strong>Total Biaya Lama:</strong> <del>${oldTotal}</del></p>
                <p style="margin-bottom: 15px;"><strong>Total Biaya Baru:</strong> <strong>${newTotal}</strong></p>
                <hr style="margin: 1rem 0;">
                <p style="margin-bottom: 5px;"><strong>Catatan dari Pengguna:</strong></p>
                <p><em>${detail.catatan_revisi || '(Tidak ada catatan)'}</em></p>
            </div>
        `,
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Setujui',
        denyButtonText: `Tolak`,
        cancelButtonText: 'Tutup',
        confirmButtonColor: '#28a745',
        denyButtonColor: '#dc3545',
    }).then((result) => {
        let action = null;
        if (result.isConfirmed) { action = 'setujui'; } 
        else if (result.isDenied) { action = 'tolak'; }
        if (action) {
            const payload = { id_rab_detail: detailId, action: action };
            api.verifikasiRevisi(payload).then(response => {
                if (response.status === 'success') {
                    dom.showToast('success', response.message);
                    dom.loadHierarchyTable('rab-container', state.selectedKegiatanId, state.selectedKroId, state.selectedBagianId);
                } else {
                    dom.showToast('error', response.message || 'Gagal memproses verifikasi.');
                }
            }).catch(() => dom.showToast('error', 'Terjadi kesalahan saat menghubungi server.'));
        }
    });
}

export const initializeEventListeners = () => {
    if (elements.pilihKegiatanBtn) {
        elements.pilihKegiatanBtn.addEventListener('click', () => {
            dom.openSelectionModal('Pilih Kegiatan', api.fetchListKegiatan(), (pilihan) => {
                state.selectedKegiatanId = pilihan.id;
                state.selectedProgramId = pilihan.programid;
                elements.kegiatanTerpilihDisplay.textContent = `Terpilih: ${pilihan.id} - ${pilihan.nama}`;
                elements.kroLabel.style.display = 'block';
                elements.kroControls.style.display = 'flex';
                elements.kroTerpilihDisplay.textContent = 'Belum Dipilih';
                elements.rabContainer.innerHTML = '<p>Langkah selanjutnya: Pilih KRO.</p>';
            });
        });
    }

    if (elements.pilihKroBtn) {
        elements.pilihKroBtn.addEventListener('click', () => {
            if (!state.selectedKegiatanId) {
                dom.showToast('error', 'Silakan pilih Kegiatan terlebih dahulu.');
                return;
            }
            dom.openSelectionModal('Pilih KRO', api.fetchKro(state.selectedKegiatanId), (pilihan) => {
                state.selectedKroId = pilihan.id;
                elements.kroTerpilihDisplay.textContent = `Terpilih: ${pilihan.id} - ${pilihan.nama}`;
                dom.loadHierarchyTable('rab-container', state.selectedKegiatanId, state.selectedKroId, 'all');
                dom.populateBagianFilter(state.selectedKegiatanId, state.selectedKroId);
            });
        });
    }

    if (elements.rabContainer) {
        elements.rabContainer.addEventListener('click', (e) => {
            const target = e.target;
            dom.hideContextMenu();

            if (target.classList.contains('toggle-btn')) {
                toggleCollapse(target);
            } else if (target.classList.contains('tambah-akun-btn')) {
                dom.openAkunModal(JSON.parse(target.dataset.context));
            } else if (target.classList.contains('tambah-detail-btn')) {
                dom.openDetailModal(target.dataset.entryId);
            } else if (target.classList.contains('edit-detail-btn')) {
                // SATU-SATUNYA LOGIKA UNTUK TOMBOL EDIT/REVISI
                const context = JSON.parse(target.closest('.rab-table-row').dataset.context);
                const mode = (state.currentStatus === 'REVISI_DRAFT') ? 'revisi' : 'edit';
                dom.openEditModal(context.id_rab_detail, context.rab_entry_id, mode);
            }
        });
        elements.rabContainer.addEventListener('contextmenu', handleContextMenu);
    }

    if (elements.filterBagian) {
        elements.filterBagian.addEventListener('change', (e) => {
            state.selectedBagianId = e.target.value;
            dom.loadHierarchyTable('rab-container', state.selectedKegiatanId, state.selectedKroId, state.selectedBagianId);
        });
    }

    if (elements.searchInput) {
        elements.searchInput.addEventListener('input', handleLiveSearch);
    }

    if (elements.modal) {
        elements.modal.addEventListener('click', (e) => {
            const target = e.target;
            if (target.id === 'btn-rekam-akun') {
                const select = document.getElementById('kode-akun-select');
                const selectedOption = select.options[select.selectedIndex];
                if (!select.value) { dom.showToast('error', 'Pilih Kode Akun.'); return; }
                const payload = { context: JSON.parse(e.target.dataset.context), id_akun: select.value, kode_akun: selectedOption.dataset.kodeakun };
                api.rekamAkun(payload).then(result => {
                    if (result.status === 'success') {
                        dom.showToast('success', result.message);
                        dom.closeAkunModal();
                        const expandedPaths = dom.getExpandedPaths();
                        dom.loadHierarchyTable('rab-container', state.selectedKegiatanId, state.selectedKroId, state.selectedBagianId, () => {
                            dom.restoreExpandedState(expandedPaths);
                        });
                    } else { dom.showToast('error', result.message); }
                });
            } else if (target.closest('.input-choice')) {
                document.querySelector('.input-choice-container').style.display = 'none';
                if (target.closest('.input-choice').dataset.choice === 'header') { dom.showHeaderForm(); } 
                else { dom.showDirectDetailForm(); }
            } else if (target.id === 'lanjutkan-ke-rincian-btn') {
                const headerInput = document.getElementById('input-header-uraian');
                if (!headerInput || !headerInput.value.trim()) { dom.showToast('error', 'Silakan isi Uraian Header terlebih dahulu.'); return; }
                dom.showDetailGridForHeader();
            } else if (target.id === 'back-to-choice-btn') {
                const entryId = elements.modal.querySelector('#simpan-semua-btn').dataset.entryId;
                dom.openDetailModal(entryId);
            } else if (target.classList.contains('add-detail-row-btn')) {
                const container = elements.modal.querySelector('#detail-rows-container');
                if (container) container.insertAdjacentHTML('beforeend', dom.createDetailInputRowHTML());
            } else if (target.classList.contains('delete-row-btn')) {
                target.closest('.detail-form-grid-row').remove();
                dom.calculateTotalBiaya(target.closest('form'));
            } else if (target.classList.contains('add-sub-detail-btn')) {
                const parentRow = e.target.closest('.detail-form-grid-row');
                let lastChild = parentRow;

                // Loop melalui baris-baris di bawahnya untuk menemukan sub-rincian terakhir
                let nextSibling = parentRow.nextElementSibling;
                while (nextSibling && nextSibling.classList.contains('sub-detail-row')) {
                    lastChild = nextSibling;
                    nextSibling = nextSibling.nextElementSibling;
    }

    // Buat baris sub-rincian baru dan sisipkan di posisi yang tepat
    const newSubRowHtml = dom.createDetailInputRowHTML({}, true);
    lastChild.insertAdjacentHTML('afterend', newSubRowHtml);
}
        });

    elements.modal.addEventListener('submit', (e) => {
        e.preventDefault();
        const form = e.target;
        if (form.id === 'add-new-detail-form' || form.id === 'input-detail-form') {
            const details = parseAndValidateDetailForm(form);
            if (!details) return;

            const payload = { 
                rab_entry_id: form.dataset.entryId, 
                details: details 
            };
            
            let apiCall; // 1. Deklarasikan variabelnya dulu

            // 2. Gunakan if/else untuk MEMILIH API yang akan digunakan
            if (state.currentStatus === 'REVISI_DRAFT') {
                // JIKA REVISI: Isi variabel dengan panggilan API revisi
                payload.catatan_revisi = "Menambah rincian baru saat revisi.";
                apiCall = api.ajukanRevisi(payload);
            } else {
                // JIKA BUKAN REVISI: Isi variabel dengan panggilan API standar
                apiCall = api.simpanDetailBaru(payload);
            }

            // 3. JALANKAN HANYA SATU PANGGILAN API yang sudah terpilih
            apiCall.then(result => {
                if (result.status === 'success') {
                    dom.showToast('success', result.message);
                    dom.closeAkunModal(); // Gunakan closeAkunModal karena ini adalah modal tambah
                    const expandedPaths = dom.getExpandedPaths();
                    dom.loadHierarchyTable('rab-container', state.selectedKegiatanId, state.selectedKroId, state.selectedBagianId, () => {
                        dom.restoreExpandedState(expandedPaths);
                    });
                } else {
                    dom.showToast('error', result.message);
                }
            }).catch(() => dom.showToast('error', 'Gagal menyimpan detail.'));
        }
    });
}

    if (elements.editModal) {
        elements.editModal.addEventListener('submit', (e) => {
            e.preventDefault();
            const form = e.target;
            const details = parseAndValidateDetailForm(form);
            if (!details) return;

            const id_detail_asli = form.dataset.idDetailAsli;
            
            let payload = {
                rab_entry_id: form.dataset.entryId,
                details: details,
                id_detail_asli: id_detail_asli 
            };
            
            let apiCall;
            let successMessage = 'Perubahan berhasil disimpan!';
            // Di dalam event listener 'submit' untuk elements.editModal
            if (state.currentStatus === 'REVISI_DRAFT') {
                // 1. AMBIL NILAI DARI TEXTAREA
                const catatanRevisiInput = form.querySelector('#input-catatan-revisi');
                const catatanRevisi = catatanRevisiInput ? catatanRevisiInput.value.trim() : '';

                // 2. Validasi: Pastikan catatan tidak kosong
                if (!catatanRevisi) {
                    dom.showToast('error', 'Catatan revisi wajib diisi.');
                    return; // Hentikan proses jika catatan kosong
                }

                // 3. Gunakan nilai dari input yang sebenarnya
                payload.catatan_revisi = catatanRevisi;
                payload.original_child_ids = JSON.parse(form.dataset.originalChildIds || '[]');
                apiCall = api.ajukanRevisi(payload);
                successMessage = 'Usulan revisi berhasil disimpan!';
            } else {
                apiCall = api.simpanBanyakDetail(payload);
            }

            const expandedPaths = dom.getExpandedPaths();
            apiCall.then(result => {
                if (result.status === 'success') {
                    dom.showToast('success', successMessage);
                    dom.closeEditModal();
                    dom.loadHierarchyTable('rab-container', state.selectedKegiatanId, state.selectedKroId, state.selectedBagianId, () => {
                        dom.restoreExpandedState(expandedPaths);
                    });
                } else {
                    dom.showToast('error', result.message || 'Gagal menyimpan data.');
                }
            }).catch(() => dom.showToast('error', 'Gagal menghubungi server.'));
        });
    
        elements.editModal.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-detail-row-btn')) {
                const container = elements.editModal.querySelector('#detail-rows-container');
                if (container) container.insertAdjacentHTML('beforeend', dom.createDetailInputRowHTML());
            }
            if (e.target.classList.contains('delete-row-btn')) {
                e.target.closest('.detail-form-grid-row').remove();
                dom.calculateTotalBiaya(e.target.closest('form'));
            }
            if (e.target.classList.contains('add-sub-detail-btn')) {
            const parentRow = e.target.closest('.detail-form-grid-row');
            const newSubRowHtml = dom.createDetailInputRowHTML({}, true);
            parentRow.insertAdjacentHTML('afterend', newSubRowHtml);
        }
        });
    }

    if (elements.updateDetailBtn) {
        elements.updateDetailBtn.addEventListener('click', () => {
            const form = elements.editModal.querySelector('#edit-detail-form');
            if (form) form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        });
    }
    
    if (elements.updateAkunBtn) {
        elements.updateAkunBtn.addEventListener('click', handleUpdateAkun);
    }

    if (elements.selectionModalClose) {
        elements.selectionModalClose.addEventListener('click', () => elements.selectionModal.classList.remove('visible'));
        elements.modalCloseBtn.addEventListener('click', () => dom.closeAkunModal());
        elements.editModalCloseBtn.addEventListener('click', () => dom.closeEditModal());
        elements.editAkunModalClose.addEventListener('click', () => dom.closeAkunEditModal());
    }

    if (elements.submitAnggaranBtn) {
        elements.submitAnggaranBtn.addEventListener('click', () => {
            Swal.fire({
                title: 'Ajukan Anggaran?',
                text: "Setelah diajukan, data tidak dapat diubah lagi sampai diverifikasi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ajukan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const payload = {
                        kegiatan_id: state.selectedKegiatanId,
                        kro_id: state.selectedKroId
                    };
                    api.submitAnggaran(payload).then(res => {
                        if (res.status === 'success') {
                            dom.showToast('success', res.message);
                            dom.loadHierarchyTable('rab-container', state.selectedKegiatanId, state.selectedKroId, state.selectedBagianId);
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    }).catch(() => Swal.fire('Error!', 'Gagal menghubungi server.', 'error'));
                }
            });
        });
    }

    if (elements.revisiAnggaranBtn) {
        elements.revisiAnggaranBtn.addEventListener('click', () => {
            Swal.fire({
                title: 'Aktifkan Mode Revisi?',
                text: "Anggaran ini akan dibuka kembali untuk diedit. Anda yakin?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Buka Revisi!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const payload = {
                        kegiatan_id: state.selectedKegiatanId,
                        kro_id: state.selectedKroId
                    };
                    api.mulaiRevisi(payload).then(res => {
                        if (res.status === 'success') {
                            dom.showToast('success', res.message);
                            dom.loadHierarchyTable('rab-container', state.selectedKegiatanId, state.selectedKroId, state.selectedBagianId);
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    }).catch(() => Swal.fire('Error!', 'Gagal menghubungi server.', 'error'));
                }
            });
        });
    }
    
    if (elements.contextMenu) {
        elements.contextMenu.addEventListener('click', (e) => {
            const action = e.target.dataset.action;
            if (!action) return;
            const context = JSON.parse(elements.contextMenu.dataset.context || '{}');
            const level = elements.contextMenu.dataset.level;
            
            if (action === 'hapus') {
                handleGenericDelete(level, context);
            } else if (action === 'tambah-akun') {
                dom.openAkunModal(context);
            } else if (action === 'tambah-detail') {
                dom.openDetailModal(context.id_rab_entry);
            } else if (action === 'edit') {
                if (context.id_rab_detail) {
                    const mode = (state.currentStatus === 'REVISI_DRAFT') ? 'revisi' : 'edit';
                    dom.openEditModal(context.id_rab_detail, context.rab_entry_id, mode);
                }
                else if (context.id_rab_entry) dom.openAkunEditModal(context.id_rab_entry, context.kode_akun);
            }
            dom.hideContextMenu();
        });
    }

    document.body.addEventListener('input', (e) => {
        const target = e.target;
        if (target.matches('.volume-input, .harga-input')) {
            if (target.matches('.harga-input')) dom.formatNumberInput(target);
            dom.calculateTotalBiaya(target.closest('form'));
        }
    });

    document.addEventListener('click', (e) => {
        if (elements.contextMenu && !elements.contextMenu.contains(e.target) && !e.target.closest('.rab-table-row')) {
            dom.hideContextMenu();
        }
    });
};