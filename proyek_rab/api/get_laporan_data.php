<?php
// api/get_laporan_data.php (Versi Final - Agregasi di Backend dengan Perbaikan Bug)
include __DIR__ . '/../includes/config/session_manager.php';
include __DIR__ . '/../includes/config/koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

// =================================================================================
// FUNGSI-FUNGSI UTAMA UNTUK PEMROSESAN DATA
// =================================================================================

function buildDetailTree(array $details): array {
    $map = [];
    $roots = [];
    foreach ($details as $detail) {
        $map[$detail['id_rab_detail']] = array_merge($detail, ['children' => []]);
    }
    foreach ($map as $id => &$node) {
        if (!empty($node['parent_detail_id']) && isset($map[$node['parent_detail_id']])) {
            $map[$node['parent_detail_id']]['children'][] = &$node;
        } else {
            $roots[] = &$node;
        }
    }
    return $roots;
}

function calculateAndAssignTreeTotals(array &$node): array {
    if (empty($node['children'])) {
        return [
            'original' => (float)($node['total_biaya'] ?? 0),
            'revisi' => (float)($node['total_biaya_revisi'] ?? $node['total_biaya'] ?? 0),
        ];
    }
    $childTotals = ['original' => 0.0, 'revisi' => 0.0];
    foreach ($node['children'] as &$child) {
        $totals = calculateAndAssignTreeTotals($child);
        $childTotals['original'] += $totals['original'];
        $childTotals['revisi'] += $totals['revisi'];
    }
    $node['total_biaya'] = $childTotals['original'];
    $node['total_biaya_revisi'] = $childTotals['revisi'];
    return $childTotals;
}

function calculateHierarchyTotals(array &$node): array {
    $total_anggaran = 0.0;
    $total_anggaran_revisi = 0.0;
    if (!empty($node['saved_data'])) {
        foreach ($node['saved_data'] as $akun) {
            $total_anggaran += (float)($akun['total_anggaran'] ?? 0);
            $total_anggaran_revisi += (float)($akun['total_anggaran_revisi'] ?? 0);
        }
    }
    if (!empty($node['children'])) {
        foreach ($node['children'] as &$childNode) {
            $childTotals = calculateHierarchyTotals($childNode);
            $total_anggaran += $childTotals['total_anggaran'];
            $total_anggaran_revisi += $childTotals['total_anggaran_revisi'];
        }
    }
    $node['total_anggaran'] = $total_anggaran;
    $node['total_anggaran_revisi'] = $total_anggaran_revisi;
    return ['total_anggaran' => $total_anggaran, 'total_anggaran_revisi' => $total_anggaran_revisi];
}

// =================================================================================
// PROSES UTAMA PENGAMBILAN DAN PEMBENTUKAN DATA
// =================================================================================

$where_clauses = []; $params = []; $types = "";
$user_role = $_SESSION['user_role'] ?? 'Guest';
$user_bagian_id = $_SESSION['bagian_id'] ?? null;

if ($user_role !== 'admin') {
    if ($user_bagian_id === null) { exit(json_encode(['summary' => [], 'hierarchy' => []])); }
    $where_clauses[] = "ro.bagian_id = ?";
    $params[] = $user_bagian_id;
    $types .= "i";
} else {
    if (!empty($_GET['biro_id']) && $_GET['biro_id'] !== 'all') { $where_clauses[] = "ro.biro_id = ?"; $params[] = (int)$_GET['biro_id']; $types .= "i"; }
    if (!empty($_GET['bagian_id']) && $_GET['bagian_id'] !== 'all') { $where_clauses[] = "ro.bagian_id = ?"; $params[] = (int)$_GET['bagian_id']; $types .= "i"; }
}
if (!empty($_GET['kegiatan_id']) && $_GET['kegiatan_id'] !== 'all') { $where_clauses[] = "ro.kegiatan_id = ?"; $params[] = (int)$_GET['kegiatan_id']; $types .= "i"; }
if (!empty($_GET['kro_id']) && $_GET['kro_id'] !== 'all') { $where_clauses[] = "ro.kro_id = ?"; $params[] = $_GET['kro_id']; $types .= "s"; }
if (!empty($_GET['status_anggaran']) && $_GET['status_anggaran'] !== 'all') { $where_clauses[] = "e.status_anggaran = ?"; $params[] = $_GET['status_anggaran']; $types .= "s"; }
$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

// --- 2. Hitung Summary ---
$summary = ['total_anggaran' => 0, 'total_pengajuan' => 0, 'menunggu_verifikasi' => 0];
$sql_summary = "SELECT SUM(COALESCE(d.total_biaya_revisi, d.total_biaya)) as total_anggaran, COUNT(DISTINCT e.id_rab_entry) as total_pengajuan, COUNT(DISTINCT CASE WHEN e.status_anggaran IN ('MENUNGGU_VERIFIKASI', 'REVISI_MENUNGGU_VERIFIKASI') THEN e.id_rab_entry END) as menunggu_verifikasi FROM rab_entry e JOIN rab_detail d ON e.id_rab_entry = d.rab_entry_id JOIN par_ro ro ON e.ro_id = ro.ro_id {$where_sql}";
$stmt_summary = mysqli_prepare($koneksi, $sql_summary);
if ($stmt_summary) {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt_summary, $types, ...$params);
    }
    mysqli_stmt_execute($stmt_summary);
    $result = mysqli_stmt_get_result($stmt_summary);
    if ($result && $summary_data = mysqli_fetch_assoc($result)) {
        $summary = array_map(fn($v) => $v ?? 0, $summary_data);
    }
    mysqli_stmt_close($stmt_summary);
}

// --- 3. Ambil Semua Data Detail ---
$flat_data = [];
// ============================= PERBAIKAN DI SINI =============================
// Kunci 'ro.kro_id' ditambahkan ke SELECT agar bisa diakses oleh PHP.
$sql_data = "SELECT keg.nama_kegiatan, kro.nama_kro, ro.kro_id, kro.kro_id as kro_kode, b.nama_bagian, ro.ro_id, ro.nama_ro, ro.no_ro, komp.komponen_id, komp.kode_komponen, komp.nama_komponen, skomp.sub_komponen_id, skomp.kode_sub_komponen, skomp.nama_sub_komponen, skomp2.sub_komponen_2_id, skomp2.kode_sub_komponen_2, skomp2.nama_sub_komponen_2, e.id_rab_entry, e.status_anggaran, akun.kode_akun, akun.uraian_akun, d.* FROM rab_entry e JOIN rab_detail d ON e.id_rab_entry = d.rab_entry_id JOIN par_ro ro ON e.ro_id = ro.ro_id JOIN par_rab_bagian b ON ro.bagian_id = b.bagian_id JOIN par_rab_kegiatan keg ON ro.kegiatan_id = keg.kegiatan_id JOIN par_rab_kro kro ON ro.kro_id = kro.kro_id JOIN par_kode_akun akun ON e.id_akun = akun.id_akun LEFT JOIN par_komponen komp ON e.komponen_id = komp.komponen_id LEFT JOIN par_sub_komponen skomp ON e.sub_komponen_id = skomp.sub_komponen_id LEFT JOIN par_sub_komponen_2 skomp2 ON e.sub_komponen_2_id = skomp2.sub_komponen_2_id {$where_sql} ORDER BY kro.kro_id, ro.no_ro, komp.kode_komponen, skomp.kode_sub_komponen, skomp2.kode_sub_komponen_2, e.id_rab_entry, d.id_rab_detail";
// =============================================================================
$stmt_data = mysqli_prepare($koneksi, $sql_data);
if ($stmt_data) {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt_data, $types, ...$params);
    }
    mysqli_stmt_execute($stmt_data);
    $result = mysqli_stmt_get_result($stmt_data);
    if ($result) {
        $flat_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    mysqli_stmt_close($stmt_data);
}

// --- 4. Bangun Hierarki dari Flat List ---
$hierarchy = [];
$detailsByEntry = [];
foreach ($flat_data as $row) {
    $detailsByEntry[$row['id_rab_entry']][] = $row;
}
foreach ($flat_data as $row) {
    if (!isset($hierarchy[$row['kro_id']])) {
        $hierarchy[$row['kro_id']] = ['id' => $row['kro_kode'], 'nama_kegiatan' => $row['nama_kegiatan'], 'nama_kro' => $row['nama_kro'], 'children' => [], 'saved_data' => []];
    }
    if (!isset($hierarchy[$row['kro_id']]['children'][$row['ro_id']])) {
        $hierarchy[$row['kro_id']]['children'][$row['ro_id']] = ['id' => $row['ro_id'], 'no_ro' => $row['no_ro'], 'nama' => $row['nama_ro'], 'children' => [], 'saved_data' => []];
    }
    $parent = &$hierarchy[$row['kro_id']]['children'][$row['ro_id']];
    if ($row['komponen_id']) {
        if (!isset($parent['children'][$row['komponen_id']])) { $parent['children'][$row['komponen_id']] = ['id' => $row['komponen_id'], 'kode' => $row['kode_komponen'], 'nama' => $row['nama_komponen'], 'children' => [], 'saved_data' => []]; }
        $parent = &$parent['children'][$row['komponen_id']];
        if ($row['sub_komponen_id']) {
            if (!isset($parent['children'][$row['sub_komponen_id']])) { $parent['children'][$row['sub_komponen_id']] = ['id' => $row['sub_komponen_id'], 'kode' => $row['kode_sub_komponen'], 'nama' => $row['nama_sub_komponen'], 'children' => [], 'saved_data' => []]; }
            $parent = &$parent['children'][$row['sub_komponen_id']];
            if ($row['sub_komponen_2_id']) {
                if (!isset($parent['children'][$row['sub_komponen_2_id']])) { $parent['children'][$row['sub_komponen_2_id']] = ['id' => $row['sub_komponen_2_id'], 'kode' => $row['kode_sub_komponen_2'], 'nama' => $row['nama_sub_komponen_2'], 'children' => [], 'saved_data' => []]; }
                $parent = &$parent['children'][$row['sub_komponen_2_id']];
            }
        }
    }
    if (!isset($parent['saved_data'][$row['id_rab_entry']])) {
        $flatDetails = $detailsByEntry[$row['id_rab_entry']] ?? [];
        $detailsTree = buildDetailTree($flatDetails);
        $accountTotals = ['original' => 0.0, 'revisi' => 0.0];
        foreach($detailsTree as &$rootNode) {
            $totals = calculateAndAssignTreeTotals($rootNode);
            $accountTotals['original'] += $totals['original'];
            $accountTotals['revisi'] += $totals['revisi'];
        }
        $parent['saved_data'][$row['id_rab_entry']] = [
            'id_rab_entry' => $row['id_rab_entry'], 'kode_akun' => $row['kode_akun'], 'uraian_akun' => $row['uraian_akun'],
            'detailsTree' => $detailsTree, 'total_anggaran' => $accountTotals['original'], 'total_anggaran_revisi' => $accountTotals['revisi'],
        ];
    }
}

// --- 5. Hitung Total Agregat Secara Rekursif ---
foreach ($hierarchy as &$kro) {
    calculateHierarchyTotals($kro);
}

// --- 6. Kirim Hasil Akhir ke Frontend ---
$finalHierarchy = array_values($hierarchy);
foreach ($finalHierarchy as &$kro) {
    if (!empty($kro['children'])) $kro['children'] = array_values($kro['children']);
    foreach ($kro['children'] as &$ro) {
        if (!empty($ro['saved_data'])) $ro['saved_data'] = array_values($ro['saved_data']);
        if (!empty($ro['children'])) $ro['children'] = array_values($ro['children']);
        foreach ($ro['children'] as &$komp) {
            if (!empty($komp['saved_data'])) $komp['saved_data'] = array_values($komp['saved_data']);
            if (!empty($komp['children'])) $komp['children'] = array_values($komp['children']);
            foreach ($komp['children'] as &$sub) {
                if (!empty($sub['saved_data'])) $sub['saved_data'] = array_values($sub['saved_data']);
                if (!empty($sub['children'])) $sub['children'] = array_values($sub['children']);
                foreach ($sub['children'] as &$sub2) {
                    if (!empty($sub2['saved_data'])) $sub2['saved_data'] = array_values($sub2['saved_data']);
                }
            }
        }
    }
}

echo json_encode(['summary' => $summary, 'hierarchy' => $finalHierarchy]);
mysqli_close($koneksi);
?>