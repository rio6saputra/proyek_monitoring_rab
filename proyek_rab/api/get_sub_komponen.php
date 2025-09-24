<?php

include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Kode status 'Forbidden'
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Silakan login terlebih dahulu.']);
    exit;
}

$komponen_id = $_GET['komponen_id'] ?? null;
$tahun_anggaran = date('Y');

if (!$komponen_id) {
    echo json_encode([]);
    exit;
}

// 1. Ambil semua sub-komponen
$sql_sub = "SELECT * FROM PAR_Sub_Komponen WHERE komponen_id = ? ORDER BY kode_sub_komponen";
$stmt_sub = mysqli_prepare($koneksi, $sql_sub);
mysqli_stmt_bind_param($stmt_sub, "s", $komponen_id);
mysqli_stmt_execute($stmt_sub);
$result_sub = mysqli_stmt_get_result($stmt_sub);

$sub_komponens = [];
$sub_komponen_ids = [];
while ($row = mysqli_fetch_assoc($result_sub)) {
    $row['saved_data'] = [];
    $sub_komponens[$row['sub_komponen_id']] = $row;
    $sub_komponen_ids[] = $row['sub_komponen_id'];
}

if (empty($sub_komponen_ids)) {
    echo json_encode([]);
    exit;
}

// 2. Ambil semua RAB Entry yang relevan untuk sub-komponen ini
$placeholders = implode(',', array_fill(0, count($sub_komponen_ids), '?'));
$sql_entry = "SELECT e.id_rab_entry, e.sub_komponen_id, a.kode_akun, a.uraian_akun 
              FROM RAB_Entry e 
              JOIN PAR_Kode_Akun a ON e.id_akun = a.id_akun
              WHERE e.sub_komponen_id IN ($placeholders) AND e.tahun_anggaran = ?";
$stmt_entry = mysqli_prepare($koneksi, $sql_entry);
$types = str_repeat('s', count($sub_komponen_ids)) . 's';
$params = array_merge($sub_komponen_ids, [$tahun_anggaran]);
mysqli_stmt_bind_param($stmt_entry, $types, ...$params);
mysqli_stmt_execute($stmt_entry);
$result_entry = mysqli_stmt_get_result($stmt_entry);

$entries_by_sub_komponen = [];
$all_entry_ids = [];
while($entry = mysqli_fetch_assoc($result_entry)){
    $entries_by_sub_komponen[$entry['sub_komponen_id']][] = $entry;
    $all_entry_ids[] = $entry['id_rab_entry'];
}

if (!empty($all_entry_ids)) {
    // 3. Ambil semua detail yang relevan
    $details_placeholder = implode(',', array_fill(0, count($all_entry_ids), '?'));
    $sql_details = "SELECT * FROM RAB_Detail WHERE rab_entry_id IN ($details_placeholder) ORDER BY id_rab_detail";
    $stmt_details = mysqli_prepare($koneksi, $sql_details);
    mysqli_stmt_bind_param($stmt_details, str_repeat('i', count($all_entry_ids)), ...$all_entry_ids);
    mysqli_stmt_execute($stmt_details);
    $result_details = mysqli_stmt_get_result($stmt_details);
    $details_by_entry = [];
    while($detail = mysqli_fetch_assoc($result_details)){
        $details_by_entry[$detail['rab_entry_id']][] = $detail;
    }

    // 4. Susun datanya dan gabungkan
    foreach ($entries_by_sub_komponen as $sub_komp_id => $entries) {
        foreach($entries as $entry) {
            $rab_entry_id = $entry['id_rab_entry'];
            if(isset($details_by_entry[$rab_entry_id])) {
                // ... (Logika membangun pohon/tree) ...
                $details_flat = $details_by_entry[$rab_entry_id];
                $tree = [];
                $lookup = [];
                foreach ($details_flat as $item) { $lookup[$item['id_rab_detail']] = $item; $lookup[$item['id_rab_detail']]['children'] = []; }
                foreach ($lookup as $id => &$item) {
                    if ($item['parent_detail_id'] != null && isset($lookup[$item['parent_detail_id']])) {
                        $lookup[$item['parent_detail_id']]['children'][] = &$item;
                    } else { $tree[] = &$item; }
                }
                $entry['details'] = $tree;
                if(isset($sub_komponens[$sub_komp_id])){
                    $sub_komponens[$sub_komp_id]['saved_data'][] = $entry;
                }
            }
        }
    }
}

echo json_encode(array_values($sub_komponens));
mysqli_close($koneksi);
?>