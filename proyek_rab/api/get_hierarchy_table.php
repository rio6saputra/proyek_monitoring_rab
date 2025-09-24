<?php
// api/get_hierarchy_table.php (Versi dengan Data Revisi)
include __DIR__ . '/../includes/config/session_manager.php';
include __DIR__ . '/../includes/config/koneksi.php';
header('Content-Type: application/json');

// Keamanan & Otorisasi
$user_role = $_SESSION['user_role'] ?? 'Guest';
$bagian_id_user = $_SESSION['bagian_id'] ?? null;
if ($user_role === 'Guest') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

$kegiatan_id = isset($_GET['kegiatan_id']) ? (int)$_GET['kegiatan_id'] : 0;
$kro_id = isset($_GET['kro_id']) ? $_GET['kro_id'] : '';
$filter_bagian_id = isset($_GET['bagian_id']) && $_GET['bagian_id'] !== 'all' ? (int)$_GET['bagian_id'] : null;

if (empty($kegiatan_id) || empty($kro_id)) {
    echo json_encode([]);
    exit;
}

// =======================================================================
// FUNGSI HELPER (Tidak Berubah)
// =======================================================================
function build_detail_tree(array &$elements, $parentId = null) { $branch = []; foreach ($elements as $key => $element) { if ($element['parent_detail_id'] == $parentId) { $children = build_detail_tree($elements, $element['id_rab_detail']); if ($children) { $element['children'] = $children; } $branch[] = $element; } } return $branch; }

function getSavedEntries($koneksi, $conditions) { 
    // Bagian 1: Mengambil data RAB_Entry (Akun) berdasarkan filter
    $sql = "SELECT e.id_rab_entry, e.komponen_id, e.sub_komponen_id, e.sub_komponen_2_id, a.id_akun, a.kode_akun, a.uraian_akun, e.memiliki_revisi, e.status_anggaran
            FROM RAB_Entry e 
            JOIN PAR_Kode_Akun a ON e.id_akun = a.id_akun 
            WHERE "; 
    $where_clauses = []; $params = []; $types = "";
    foreach($conditions as $key => $value) {
        if ($value === null) {
            $where_clauses[] = "e.{$key} IS NULL";
        } else {
            $where_clauses[] = "e.{$key} = ?";
            $params[] = $value;
            $types .= is_int($value) ? "i" : "s";
        }
    }
    $sql .= implode(" AND ", $where_clauses);
    $stmt = mysqli_prepare($koneksi, $sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $entries = [];
    $entry_ids = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['total_anggaran'] = 0;
        $entries[$row['id_rab_entry']] = $row;
        $entry_ids[] = $row['id_rab_entry'];
    }
    
    // Bagian 2: Jika ada data Akun, ambil semua Rincian Detail yang terkait
    if (!empty($entry_ids)) { 
        $placeholders = implode(',', array_fill(0, count($entry_ids), '?'));
        
        // Mengambil semua Rincian Detail dalam satu query
        $sql_details = "SELECT * FROM RAB_Detail WHERE rab_entry_id IN ($placeholders)";
        $stmt_details = mysqli_prepare($koneksi, $sql_details);
        mysqli_stmt_bind_param($stmt_details, str_repeat('i', count($entry_ids)), ...$entry_ids);
        mysqli_stmt_execute($stmt_details);
        
        // BARIS PENTING YANG HILANG: Mengambil hasil query
        $result_details = mysqli_stmt_get_result($stmt_details);
        
        // Kelompokkan semua Rincian Detail berdasarkan ID Akun-nya
        $details_by_entry = [];
        while($detail = mysqli_fetch_assoc($result_details)){
            $details_by_entry[$detail['rab_entry_id']][] = $detail;
        }

        // Bagian 3: Proses Rincian Detail dan hitung total anggaran
        foreach($details_by_entry as $entry_id => &$entry_details) { // Gunakan reference
            $entries[$entry_id]['details'] = build_detail_tree($entry_details);
            calculate_detail_header_totals($entries[$entry_id]['details']);
            
            // --- PERBAIKAN PERHITUNGAN TOTAL ---
            $total_entry = 0;
            $total_entry_revisi = 0;

            foreach ($entries[$entry_id]['details'] as $top_level_detail) {
                $total_entry += (float)($top_level_detail['total_biaya'] ?? 0);

                // Jika ada revisi, gunakan total revisi. Jika tidak, total revisi = total utama.
                $total_entry_revisi += (float)($top_level_detail['total_biaya_revisi'] ?? $top_level_detail['total_biaya'] ?? 0);
            }
            $entries[$entry_id]['total_anggaran'] = $total_entry;
            $entries[$entry_id]['total_anggaran_revisi'] = $total_entry_revisi;
            // --- PERBAIKAN SELESAI ---
        }
    }
    
    return array_values($entries); 
}

function calculate_total(&$hierarchy) {
    $total = 0;
    $total_revisi = 0;

    // Rekursif untuk semua level di bawahnya
    if (isset($hierarchy['sub_komponens_2'])) { 
        foreach ($hierarchy['sub_komponens_2'] as &$sk2) { 
            $child_totals = calculate_total($sk2); 
            $total += $child_totals['total']; 
            $total_revisi += $child_totals['total_revisi']; 
        } 
    }
    if (isset($hierarchy['sub_komponens'])) { 
        foreach ($hierarchy['sub_komponens'] as &$sk) { 
            $child_totals = calculate_total($sk); 
            $total += $child_totals['total']; 
            $total_revisi += $child_totals['total_revisi']; 
        } 
    }
    if (isset($hierarchy['komponens'])) { 
        foreach ($hierarchy['komponens'] as &$komp) { 
            $child_totals = calculate_total($komp); 
            $total += $child_totals['total']; 
            $total_revisi += $child_totals['total_revisi']; 
        } 
    }

    // Jumlahkan 'saved_data' di level saat ini
    if (isset($hierarchy['saved_data'])) {
        foreach ($hierarchy['saved_data'] as $saved) {
            $total += (float)($saved['total_anggaran'] ?? 0);
            $total_revisi += (float)($saved['total_anggaran_revisi'] ?? $saved['total_anggaran'] ?? 0);
        }
    }
    
    $hierarchy['total_anggaran'] = $total;
    $hierarchy['total_anggaran_revisi'] = $total_revisi;
    
    return ['total' => $total, 'total_revisi' => $total_revisi];
}

function calculate_detail_header_totals(array &$details) {
    foreach ($details as &$item) {
        // Cek jika item ini adalah sebuah header (memiliki anak)
        if (!empty($item['children'])) {
            // Jalankan fungsi ini untuk level di bawahnya terlebih dahulu (rekursif)
            calculate_detail_header_totals($item['children']);

            // Sekarang, hitung total untuk header ini
            $children_sum = 0;
            $children_sum_revisi = 0;
            $has_revisi_child = false;

            foreach ($item['children'] as $child) {
                // Kalkulasi total biaya original
                $children_sum += (float)$child['total_biaya'];

                // Kalkulasi total biaya revisi (jika ada)
                if (isset($child['total_biaya_revisi'])) {
                    $children_sum_revisi += (float)$child['total_biaya_revisi'];
                    $has_revisi_child = true;
                } else {
                    // Jika anak tidak punya revisi, gunakan biaya originalnya untuk total revisi
                    $children_sum_revisi += (float)$child['total_biaya'];
                }
            }
            
            // Update total biaya header
            $item['total_biaya'] = $children_sum;

            // Update total biaya revisi header HANYA jika ada anak yang direvisi
            if ($has_revisi_child) {
                 $item['total_biaya_revisi'] = $children_sum_revisi;
            }
        }
    }
}

// Query Utama untuk mengambil RO
$sql_ro = "SELECT * FROM PAR_RO WHERE kegiatan_id = ? AND kro_id = ?";
$params = [$kegiatan_id, $kro_id];
$types = "is";

if ($user_role !== 'admin' && $bagian_id_user !== null) {
    $sql_ro .= " AND bagian_id = ?";
    $params[] = $bagian_id_user;
    $types .= "i";
} elseif ($user_role === 'admin' && $filter_bagian_id) {
    $sql_ro .= " AND bagian_id = ?";
    $params[] = $filter_bagian_id;
    $types .= "i";
}

$sql_ro .= " ORDER BY no_ro";
$stmt_ro = mysqli_prepare($koneksi, $sql_ro);
mysqli_stmt_bind_param($stmt_ro, $types, ...$params);
mysqli_stmt_execute($stmt_ro);
$result_ro = mysqli_stmt_get_result($stmt_ro);

$hierarchy = [];
while ($ro = mysqli_fetch_assoc($result_ro)) {
    $ro['saved_data'] = getSavedEntries($koneksi, ['ro_id' => $ro['ro_id'], 'komponen_id' => null, 'sub_komponen_id' => null, 'sub_komponen_2_id' => null]);
    $ro['total_anggaran'] = 0;
    $sql_komp = "SELECT * FROM PAR_Komponen WHERE ro_id = ? ORDER BY kode_komponen";
    $stmt_komp = mysqli_prepare($koneksi, $sql_komp);
    mysqli_stmt_bind_param($stmt_komp, "s", $ro['ro_id']);
    mysqli_stmt_execute($stmt_komp);
    $result_komp = mysqli_stmt_get_result($stmt_komp);
    $ro['komponens'] = [];
    while ($komp = mysqli_fetch_assoc($result_komp)) {
        $komp['saved_data'] = getSavedEntries($koneksi, ['komponen_id' => $komp['komponen_id'], 'sub_komponen_id' => null, 'sub_komponen_2_id' => null]);
        $komp['total_anggaran'] = 0;
        $sql_sub_komp = "SELECT * FROM PAR_Sub_Komponen WHERE komponen_id = ? ORDER BY kode_sub_komponen";
        $stmt_sub_komp = mysqli_prepare($koneksi, $sql_sub_komp);
        mysqli_stmt_bind_param($stmt_sub_komp, "s", $komp['komponen_id']);
        mysqli_stmt_execute($stmt_sub_komp);
        $result_sub_komp = mysqli_stmt_get_result($stmt_sub_komp);
        $komp['sub_komponens'] = [];
        while ($sub_komp = mysqli_fetch_assoc($result_sub_komp)) {
            $sub_komp['saved_data'] = getSavedEntries($koneksi, ['sub_komponen_id' => $sub_komp['sub_komponen_id'], 'sub_komponen_2_id' => null]);
            $sub_komp['total_anggaran'] = 0;
            $sql_sub_komp2 = "SELECT * FROM PAR_Sub_Komponen_2 WHERE sub_komponen_id = ? ORDER BY kode_sub_komponen_2";
            $stmt_sub_komp2 = mysqli_prepare($koneksi, $sql_sub_komp2);
            mysqli_stmt_bind_param($stmt_sub_komp2, "s", $sub_komp['sub_komponen_id']);
            mysqli_stmt_execute($stmt_sub_komp2);
            $result_sub_komp2 = mysqli_stmt_get_result($stmt_sub_komp2);
            $sub_komp['sub_komponens_2'] = [];
            while ($sub_komp2 = mysqli_fetch_assoc($result_sub_komp2)) {
                $sub_komp2['saved_data'] = getSavedEntries($koneksi, ['sub_komponen_2_id' => $sub_komp2['sub_komponen_2_id']]);
                $sub_komp2['total_anggaran'] = 0;
                $sub_komp['sub_komponens_2'][] = $sub_komp2;
            }
            $komp['sub_komponens'][] = $sub_komp;
        }
        $ro['komponens'][] = $komp;
    }
    $hierarchy[] = $ro;
}

foreach ($hierarchy as &$ro_item) {
    calculate_total($ro_item);
}

echo json_encode($hierarchy);
mysqli_close($koneksi);
?>