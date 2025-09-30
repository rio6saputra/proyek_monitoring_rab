<?php
// api/rekam_akun.php (Versi Final dengan Penanganan Status Revisi)
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (empty($data['context']) || empty($data['id_akun']) || empty($data['kode_akun'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

$context = $data['context'];
$id_akun = $data['id_akun'];
$kode_akun = $data['kode_akun'];
$tahun_anggaran = date('Y');
$user_id = $_SESSION['user_id'];
// --- PERBAIKAN DI SINI: Ambil status dari frontend ---
$current_status = $data['current_status'] ?? 'DRAFT';
$status_anggaran = ($current_status === 'REVISI_DRAFT') ? 'REVISI_DRAFT' : 'DRAFT';

// =======================================================================
// PERBAIKAN LOGIKA UTAMA ADA DI SINI
// =======================================================================
$ro_id = $context['ro_id'] ?? null;
$komponen_id = $context['komponen_id'] ?? null;
$sub_komponen_id = $context['sub_komponen_id'] ?? null;
$sub_komponen_2_id = $context['sub_komponen_2_id'] ?? null;

// Jika ro_id tidak ada langsung, cari dari parent
if (empty($ro_id)) {
    if (!empty($komponen_id)) {
        $stmt = mysqli_prepare($koneksi, "SELECT ro_id FROM par_komponen WHERE komponen_id = ?");
        mysqli_stmt_bind_param($stmt, "s", $komponen_id);
    } elseif (!empty($sub_komponen_id)) {
        $stmt = mysqli_prepare($koneksi, "SELECT k.ro_id FROM par_sub_komponen sk JOIN par_komponen k ON sk.komponen_id = k.komponen_id WHERE sk.sub_komponen_id = ?");
        mysqli_stmt_bind_param($stmt, "s", $sub_komponen_id);
    } elseif (!empty($sub_komponen_2_id)) {
         $stmt = mysqli_prepare($koneksi, "SELECT k.ro_id FROM par_sub_komponen_2 sk2 JOIN par_sub_komponen sk ON sk2.sub_komponen_id = sk.sub_komponen_id JOIN par_komponen k ON sk.komponen_id = k.komponen_id WHERE sk2.sub_komponen_2_id = ?");
         mysqli_stmt_bind_param($stmt, "s", $sub_komponen_2_id);
    }

    if (isset($stmt)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $ro_id = $row['ro_id'];
        }
    }
}

if (empty($ro_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menentukan RO Induk. Kolom ro_id tidak boleh null.']);
    exit;
}
// =======================================================================

mysqli_begin_transaction($koneksi);

try {
    // --- PERBAIKAN DI SINI: Tambahkan kolom status_anggaran ke query ---
    $sql_entry = "INSERT INTO RAB_Entry (ro_id, komponen_id, sub_komponen_id, sub_komponen_2_id, id_akun, kode_akun, tahun_anggaran, created_by_user_id, status_anggaran) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_entry = mysqli_prepare($koneksi, $sql_entry);
    mysqli_stmt_bind_param($stmt_entry, "ssssisiis", $ro_id, $komponen_id, $sub_komponen_id, $sub_komponen_2_id, $id_akun, $kode_akun, $tahun_anggaran, $user_id, $status_anggaran);
    
    if (!mysqli_stmt_execute($stmt_entry)) {
        throw new Exception("Gagal mengeksekusi statement: " . mysqli_stmt_error($stmt_entry));
    }
    
    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Akun berhasil direkam!']);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>