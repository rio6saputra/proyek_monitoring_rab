// js/api.js (Versi Final dengan Alur Filter Dinamis & Format Konsisten)

const apiCall = (url, options = {}) => {
    return fetch(url, options).then(res => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        return res.json();
    });
};

export const fetchListBagian = (kegiatanId, kroId) => {
    return apiCall(`api/get_list_bagian.php?kegiatan_id=${kegiatanId}&kro_id=${kroId}`);
};

export const fetchListKegiatan = () => {
    return apiCall('api/get_list_kegiatan.php');
};

export const fetchKro = (kegiatanId) => {
    return apiCall(`api/get_kro.php?kegiatan_id=${kegiatanId}`);
};

export const fetchHierarchyTable = (kegiatanId, kroId, bagianId = null) => {
    let url = `api/get_hierarchy_table.php?kegiatan_id=${kegiatanId}&kro_id=${kroId}`;
    if (bagianId && bagianId !== 'all') {
        url += `&bagian_id=${bagianId}`;
    }
    return apiCall(url);
};

export const fetchKodeAkun = () => {
    return apiCall('api/get_kode_akun.php');
};

const postApiCall = (endpoint, payload) => {
    return apiCall(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    });
};

export const rekamAkun = (payload) => {
    return postApiCall('api/rekam_akun.php', payload);
};
export const simpanDetail = (payload) => {
    return postApiCall('api/simpan_detail.php', payload);
};
export const updateDetail = (payload) => {
    return postApiCall('api/update_detail.php', payload);
};
export const editAkun = (payload) => {
    return postApiCall('api/edit_akun.php', payload);
};
export const simpanBanyakDetail = (payload) => {
    return postApiCall('api/simpan_banyak_detail.php', payload);
};
export const hapusRo = (payload) => {
    return postApiCall('api/hapus_ro.php', payload);
};
export const hapusKomponen = (payload) => {
    return postApiCall('api/hapus_komponen.php', payload);
};
export const hapusSubKomponen = (payload) => {
    return postApiCall('api/hapus_sub_komponen.php', payload);
};
export const hapusSubKomponen2 = (payload) => {
    return postApiCall('api/hapus_sub_komponen_2.php', payload);
};
export const hapusAkun = (payload) => {
    return postApiCall('api/hapus_akun.php', payload);
};
export const hapusDetail = (payload) => {
    return postApiCall('api/hapus_detail.php', payload);
};
export const fetchLaporanFilters = () => {
    return apiCall('api/get_laporan_filters.php');
};

export const fetchLaporanData = (filters) => {
    const params = new URLSearchParams(filters);
    return apiCall(`api/get_laporan_data.php?${params.toString()}`);
};

export const ajukanRevisi = (payload) => {
    return postApiCall('api/ajukan_revisi.php', payload);
};
export const verifikasiRevisi = (payload) => {
    return postApiCall('api/verifikasi_revisi.php', payload);
};
export const simpanDetailBaru = (payload) => {
    return postApiCall('api/simpan_detail_baru.php', payload);
};
export const submitAnggaran = (payload) => {
    return postApiCall('api/submit_anggaran.php', payload);
};
export const fetchPengajuan = () => {
    return apiCall('api/get_pengajuan.php');
};
export const verifikasiAnggaran = (payload) => {
    return postApiCall('api/verifikasi_anggaran.php', payload);
};
export const mulaiRevisi = (payload) => {
    return postApiCall('api/mulai_revisi.php', payload);
};