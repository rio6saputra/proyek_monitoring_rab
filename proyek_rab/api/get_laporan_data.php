<?php
// api/get_laporan_data.php (Versi Final Stabil v3)
include __DIR__ . '/../includes/config/session_manager.php';
include __DIR__ . '/../includes/config/koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

function build_detail_tree(array &$elements, $parentId = null) {
    $branch = [];
    foreach ($elements as $key => $element) {
        if ($element['parent_detail_id'] == $parentId) {
            $children = build_detail_tree($elements, $element['id_rab_detail']);
            if ($children) { $element['children'] = $children; }
            $branch[] = $element;
        }
    }
    return $branch;
}

function calculate_total(&$hierarchy) {
    $total = 0;
    if (isset($hierarchy['sub_komponens_2'])) { foreach ($hierarchy['sub_komponens_2'] as &$sk2) { $total += calculate_total($sk2); } }
    if (isset($hierarchy['sub_komponens'])) { foreach ($hierarchy['sub_komponens'] as &$sk) { $total += calculate_total($sk); } }
    if (isset($hierarchy['komponens'])) { foreach ($hierarchy['komponens'] as &$komp) { $total += calculate_total($komp); } }
    if (isset($hierarchy['saved_data'])) { foreach ($hierarchy['saved_data'] as $saved) { $total += $saved['total_anggaran']; } }
    $hierarchy['total_anggaran'] = $total;
    return $total;
}

$biro_id = isset($_GET['biro_id']) && $_GET['biro_id'] !== 'all' ? (int)$_GET['biro_id'] : null;
$bagian_id = isset($_GET['bagian_id']) && $_GET['bagian_id'] !== 'all' ? (int)$_GET['bagian_id'] : null;

$sql = "
    SELECT
        ro.ro_id, ro.nama_ro, ro.no_ro, ro.kegiatan_id, ro.kro_id,
        k.komponen_id, k.kode_komponen, k.nama_komponen,
        sk.sub_komponen_id, sk.kode_sub_komponen, sk.nama_sub_komponen,
        sk2.sub_komponen_2_id, sk2.kode_sub_komponen_2, sk2.nama_sub_komponen_2,
        e.id_rab_entry, e.id_akun AS entry_id_akun,
        akun.kode_akun, akun.uraian_akun,
        d.id_rab_detail, d.parent_detail_id, d.uraian_pekerjaan, d.harga_satuan, d.total_biaya, d.catatan,
        v.id_rab_volume, v.volume, v.satuan
    FROM PAR_RO ro
    LEFT JOIN PAR_Komponen k ON ro.ro_id = k.ro_id
    LEFT JOIN PAR_Sub_Komponen sk ON k.komponen_id = sk.komponen_id
    LEFT JOIN PAR_Sub_Komponen_2 sk2 ON sk.sub_komponen_id = sk2.sub_komponen_id
    -- ===================================================================
    -- [PERBAIKAN KUNCI] Logika JOIN RAB_Entry dibuat jauh lebih presisi
    -- ===================================================================
    LEFT JOIN RAB_Entry e ON
        -- Entri untuk Sub-Komponen 2 HANYA cocok jika ID-nya sama
        e.sub_komponen_2_id = sk2.sub_komponen_2_id OR
        -- Entri untuk Sub-Komponen 1 HANYA cocok jika ID-nya sama DAN baris ini BUKAN baris Sub-Komponen 2
        (e.sub_komponen_id = sk.sub_komponen_id AND e.sub_komponen_2_id IS NULL AND sk2.sub_komponen_2_id IS NULL) OR
        -- Entri untuk Komponen HANYA cocok jika ID-nya sama DAN baris ini BUKAN baris Sub-Komponen
        (e.komponen_id = k.komponen_id AND e.sub_komponen_id IS NULL AND sk.sub_komponen_id IS NULL) OR
        -- Entri untuk RO HANYA cocok jika ID-nya sama DAN baris ini BUKAN baris Komponen
        (e.ro_id = ro.ro_id AND e.komponen_id IS NULL AND k.komponen_id IS NULL)
    -- ===================================================================
    LEFT JOIN PAR_Kode_Akun akun ON e.id_akun = akun.id_akun
    LEFT JOIN RAB_Detail d ON e.id_rab_entry = d.rab_entry_id
    LEFT JOIN RAB_Volume v ON d.id_rab_detail = v.rab_detail_id
";

$where_clauses = [];
$params = [];
$types = "";
if ($biro_id) { $where_clauses[] = "ro.biro_id = ?"; $params[] = $biro_id; $types .= "i"; }
if ($bagian_id) { $where_clauses[] = "ro.bagian_id = ?"; $params[] = $bagian_id; $types .= "i"; }
if (!empty($where_clauses)) { $sql .= " WHERE " . implode(" AND ", $where_clauses); }

$sql .= " ORDER BY ro.kegiatan_id, ro.kro_id, ro.no_ro, k.kode_komponen, sk.kode_sub_komponen, sk2.kode_sub_komponen_2, akun.kode_akun, d.id_rab_detail";

$stmt = mysqli_prepare($koneksi, $sql);
if ($stmt && !empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$hierarchy = [];
while ($row = mysqli_fetch_assoc($result)) {
    $ro_id = $row['ro_id'];
    if ($ro_id) {
        if (!isset($hierarchy[$ro_id])) {
            $hierarchy[$ro_id] = ['ro_id' => $row['ro_id'], 'nama_ro' => $row['nama_ro'], 'no_ro' => $row['no_ro'], 'kegiatan_id' => $row['kegiatan_id'], 'kro_id' => $row['kro_id'], 'komponens' => []];
        }
        $komp_id = $row['komponen_id'];
        if ($komp_id) {
            if (!isset($hierarchy[$ro_id]['komponens'][$komp_id])) {
                $hierarchy[$ro_id]['komponens'][$komp_id] = ['komponen_id' => $row['komponen_id'], 'kode_komponen' => $row['kode_komponen'], 'nama_komponen' => $row['nama_komponen'], 'sub_komponens' => []];
            }
            $sub_komp_id = $row['sub_komponen_id'];
            if ($sub_komp_id) {
                if (!isset($hierarchy[$ro_id]['komponens'][$komp_id]['sub_komponens'][$sub_komp_id])) {
                    $hierarchy[$ro_id]['komponens'][$komp_id]['sub_komponens'][$sub_komp_id] = ['sub_komponen_id' => $row['sub_komponen_id'], 'kode_sub_komponen' => $row['kode_sub_komponen'], 'nama_sub_komponen' => $row['nama_sub_komponen'], 'sub_komponens_2' => []];
                }
                $sub_komp2_id = $row['sub_komponen_2_id'];
                if ($sub_komp2_id && !isset($hierarchy[$ro_id]['komponens'][$komp_id]['sub_komponens'][$sub_komp_id]['sub_komponens_2'][$sub_komp2_id])) {
                    $hierarchy[$ro_id]['komponens'][$komp_id]['sub_komponens'][$sub_komp_id]['sub_komponens_2'][$sub_komp2_id] = ['sub_komponen_2_id' => $row['sub_komponen_2_id'], 'kode_sub_komponen_2' => $row['kode_sub_komponen_2'], 'nama_sub_komponen_2' => $row['nama_sub_komponen_2'], 'saved_data' => []];
                }
            }
        }
    }

    $entry_id = $row['id_rab_entry'];
    if ($entry_id) {
        $parent_level = null;
        if (!empty($komp_id) && !empty($sub_komp_id) && !empty($sub_komp2_id)) $parent_level = &$hierarchy[$ro_id]['komponens'][$komp_id]['sub_komponens'][$sub_komp_id]['sub_komponens_2'][$sub_komp2_id];
        elseif (!empty($komp_id) && !empty($sub_komp_id)) $parent_level = &$hierarchy[$ro_id]['komponens'][$komp_id]['sub_komponens'][$sub_komp_id];
        elseif (!empty($komp_id)) $parent_level = &$hierarchy[$ro_id]['komponens'][$komp_id];
        elseif (!empty($ro_id)) $parent_level = &$hierarchy[$ro_id];
        
        if ($parent_level) {
            if (!isset($parent_level['saved_data'])) $parent_level['saved_data'] = [];
            if (!isset($parent_level['saved_data'][$entry_id])) { $parent_level['saved_data'][$entry_id] = ['id_rab_entry' => $entry_id, 'id_akun' => $row['entry_id_akun'], 'kode_akun' => $row['kode_akun'], 'uraian_akun' => $row['uraian_akun'], 'total_anggaran' => 0, 'details_flat' => []]; }
            
            $detail_id = $row['id_rab_detail'];
            if ($detail_id) {
                if (!isset($parent_level['saved_data'][$entry_id]['details_flat'][$detail_id])) {
                    $parent_level['saved_data'][$entry_id]['details_flat'][$detail_id] = ['id_rab_detail' => $detail_id, 'parent_detail_id' => $row['parent_detail_id'], 'uraian_pekerjaan' => $row['uraian_pekerjaan'], 'harga_satuan' => $row['harga_satuan'], 'total_biaya' => $row['total_biaya'], 'catatan' => $row['catatan'], 'rab_entry_id' => $entry_id, 'volumes' => []];
                    $parent_level['saved_data'][$entry_id]['total_anggaran'] += (float)$row['total_biaya'];
                }
                if ($row['id_rab_volume']) { $parent_level['saved_data'][$entry_id]['details_flat'][$detail_id]['volumes'][$row['id_rab_volume']] = ['volume' => $row['volume'], 'satuan' => $row['satuan']]; }
            }
        }
    }
}

foreach ($hierarchy as &$ro_data) {
    if (isset($ro_data['saved_data'])) { foreach ($ro_data['saved_data'] as &$entry_data) { $details_flat = array_values($entry_data['details_flat'] ?? []); foreach($details_flat as &$detail) { $detail['volumes'] = array_values($detail['volumes'] ?? []); } $entry_data['details'] = build_detail_tree($details_flat); unset($entry_data['details_flat']); } $ro_data['saved_data'] = array_values($ro_data['saved_data']); }
    foreach (($ro_data['komponens'] ?? []) as &$komp_data) {
        if (isset($komp_data['saved_data'])) { foreach ($komp_data['saved_data'] as &$entry_data) { $details_flat = array_values($entry_data['details_flat'] ?? []); foreach($details_flat as &$detail) { $detail['volumes'] = array_values($detail['volumes'] ?? []); } $entry_data['details'] = build_detail_tree($details_flat); unset($entry_data['details_flat']); } $komp_data['saved_data'] = array_values($komp_data['saved_data']); }
        foreach (($komp_data['sub_komponens'] ?? []) as &$sub_data) {
            if (isset($sub_data['saved_data'])) { foreach ($sub_data['saved_data'] as &$entry_data) { $details_flat = array_values($entry_data['details_flat'] ?? []); foreach($details_flat as &$detail) { $detail['volumes'] = array_values($detail['volumes'] ?? []); } $entry_data['details'] = build_detail_tree($details_flat); unset($entry_data['details_flat']); } $sub_data['saved_data'] = array_values($sub_data['saved_data']); }
            foreach (($sub_data['sub_komponens_2'] ?? []) as &$sub2_data) {
                if (isset($sub2_data['saved_data'])) { foreach ($sub2_data['saved_data'] as &$entry_data) { $details_flat = array_values($entry_data['details_flat'] ?? []); foreach($details_flat as &$detail) { $detail['volumes'] = array_values($detail['volumes'] ?? []); } $entry_data['details'] = build_detail_tree($details_flat); unset($entry_data['details_flat']); } $sub2_data['saved_data'] = array_values($sub2_data['saved_data']); }
            }
            $sub_data['sub_komponens_2'] = array_values($sub_data['sub_komponens_2'] ?? []);
        }
        $komp_data['sub_komponens'] = array_values($komp_data['sub_komponens'] ?? []);
    }
    $ro_data['komponens'] = array_values($ro_data['komponens'] ?? []);
}

$final_hierarchy = array_values($hierarchy);
foreach ($final_hierarchy as &$ro_item) { calculate_total($ro_item); }

echo json_encode($final_hierarchy);
mysqli_close($koneksi);
?>